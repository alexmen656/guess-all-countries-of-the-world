<template>
  <header class="app-header">
    <h1>Name all countries of the World!</h1>
    <p class="app-description">
      In this game, you get two countries, and your job is to guess other
      countries that connect them by sharing borders. Each country you guess has
      to touch the previous one, like building a chain. If you guess a country
      that's not in the chain, it won't help you connect the two countries. if
      you misspell a country's name, it won't be accepted, so be careful with
      your spelling!
    </p>
  </header>
  <div id="map" style="width: 100%; height: 100vh"></div>
  <div id="floating-input">
    <h1>Name a countrie</h1>
    <form @submit.prevent="submitCountrie">
      <input
        type="text"
        v-model="country"
        placeholder="Enter country name and press Enter"
      />
    </form>
  </div>
  <button id="reload-button" @click="reloadGame">&#x21bb;</button>

  <div v-if="showModal" class="modal">
    <div class="modal-content">
      <span class="close" @click="closeModal">&times;</span>
      <h2>Congratulations!</h2>
      <p>You have successfully connected with .</p>
      <button @click="reloadGame">Play Again</button>
    </div>
  </div>
</template>

<script>
import { shallowRef } from "vue";
import {
  //getRandomCountryPair,
  //isConnected,
  formatCountryName,
  isValidCountry,
} from "../getCountries.js";

export default {
  name: "App",
  data() {
    return {
      showModal: false,
      map: shallowRef(null),
      geojson: [],
      guessedCountries: [],
      country: "",
      showOutline: true,
    };
  },
  async mounted() {
    await this.initMap();
  },
  methods: {
    async initMap() {
      await window.mapkit.init({
        authorizationCallback: function (done) {
          fetch("https://alex.polan.sk/name-the-countries/verify.php")
            .then((res) => res.text())
            .then(done);
        },
        language: "en",
      });

      const region = new window.mapkit.CoordinateRegion(
        new window.mapkit.Coordinate(25.0, 15.0),
        new window.mapkit.CoordinateSpan(180.0, 360.0)
      );

      const map = new window.mapkit.Map("map", {
        mapType: window.mapkit.Map.MapTypes.Satellite,
        center: new window.mapkit.Coordinate(25.0, 15.0),
        region: region,
      });
      map.showsCompass = window.mapkit.FeatureVisibility.Visible;

      let geoJSONParserDelegate = {
        itemForPolygon: (overlay) => {
          const strokeOpacity = this.showOutline ? 0.8 : 0;
          const lineWidth = this.showOutline ? 1 : 0;

          overlay.style = new window.mapkit.Style({
            strokeColor: "#000",
            strokeOpacity: strokeOpacity,
            lineWidth: lineWidth,
            fillOpacity: 0.8,
            fillColor: "#CACACA",
          });

          map.addOverlay(overlay);
          return overlay;
        },

        geoJSONDidComplete: function (result, geoJSON) {
          console.log("GeoJSONDelegate.geoJSONDidComplete");
          console.log(result);
          console.log(geoJSON);
        },
        geoJSONDidError: function (error, geoJSON) {
          console.log("GeoJSONDelegate.geoJSONDidError");
          console.log(error);
          console.log(geoJSON);
        },
      };

      if (this.geojson.length === 0) {
        this.$axios.get("countries.php").then((response) => {
          let data = response.data;
          this.geojson = JSON.parse(JSON.stringify(response.data));
          window.mapkit.importGeoJSON(data, geoJSONParserDelegate);
        });
      } else {
        const data2 = JSON.parse(JSON.stringify(this.geojson));
        let data = data2;
        data.features.forEach((feature) => {
          if (
            formatCountryName(feature.properties.NAME) ===
              formatCountryName(this.countryPair.from) ||
            formatCountryName(feature.properties.NAME) ===
              formatCountryName(this.countryPair.to)
          ) {
            feature.properties.count = 15;
          }
        });
        window.mapkit.importGeoJSON(data, geoJSONParserDelegate);
      }
    },
    submitCountrie() {
      if (this.country.trim()) {
        if (isValidCountry(this.country)) {
          if (!this.guessedCountries.includes(this.country)) {
            this.guessedCountries.push(this.country);
            this.updateMap();
            this.country = "";
          }
        } else {
          alert(this.country + " is not a valid country name!");
        }
      } else {
        alert("Please enter a country name!");
      }
    },
    updateMap() {
      let geoJSONParserDelegate = {
        itemForPolygon: (overlay) => {
          const strokeOpacity = this.showOutline ? 0.8 : 0;
          const lineWidth = this.showOutline ? 1 : 0;

          overlay.style = new window.mapkit.Style({
            strokeColor: "#000",
            strokeOpacity: strokeOpacity,
            lineWidth: lineWidth,
            fillOpacity: 0.8,
            fillColor: "#CACACA",
          });
          this.map.addOverlay(overlay);
          return overlay;
        },

        itemForFeature: (overlay, geoJSON) => {
          const isGuessed = geoJSON.properties.guessed;
          const fillColor = isGuessed ? "#008000" : "#CACACA";
          const strokeOpacity = this.showOutline ? 0.8 : 0;
          const lineWidth = this.showOutline ? 1 : 0;

          overlay.data = {
            name: geoJSON.properties.name_en,
            isGuessed: isGuessed,
          };

          overlay.style = new window.mapkit.Style({
            fillOpacity: 0.7,
            lineWidth: lineWidth,
            strokeOpacity: strokeOpacity,
            fillColor: fillColor,
          });

          return overlay;
        },

        geoJSONDidComplete: function (result, geoJSON) {
          console.log("GeoJSONDelegate.geoJSONDidComplete");
          console.log(result);
          console.log(geoJSON);
        },
        geoJSONDidError: function (error, geoJSON) {
          console.log("GeoJSONDelegate.geoJSONDidError");
          console.log(error);
          console.log(geoJSON);
        },
      };

      if (this.geojson.length === 0) {
        this.$axios.get("countries.php").then((response) => {
          this.geojson = JSON.parse(JSON.stringify(response.data));
          let data = response.data;
          data.features.forEach((feature) => {
            if (
              this.guessedCountries.includes(
                formatCountryName(feature.properties.name_en)
              )
            ) {
              feature.properties.guessed = true;
            }
          });

          this.map.overlays.forEach((overlay) =>
            this.map.removeOverlay(overlay)
          );
          window.mapkit.importGeoJSON(data, geoJSONParserDelegate);
        });
      } else {
        const data2 = JSON.parse(JSON.stringify(this.geojson));
        let data = data2;
        data.features.forEach((feature) => {
          if (
            this.guessedCountries.includes(
              formatCountryName(feature.properties.name_en)
            )
          ) {
            feature.properties.guessed = true;
          }
        });

        this.map.overlays.forEach((overlay) => this.map.removeOverlay(overlay));
        window.mapkit.importGeoJSON(data, geoJSONParserDelegate);
      }
    },
  },
};
</script>

<style scoped>
#map {
  width: 100%;
  height: 100%;
}

#map::before {
  content: "";
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  pointer-events: none;
  background: radial-gradient(
    circle,
    rgba(0, 0, 0, 0) 60%,
    rgba(0, 0, 0, 0.6) 100%
  );
  z-index: 1000;
}

#floating-input {
  position: absolute;
  bottom: 20px;
  left: 50%;
  transform: translateX(-50%);
  width: 80%;
  text-align: center;
  background-color: rgba(255, 255, 255, 0.6);
  padding: 10px;
  border-radius: 24px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

#floating-input h1 {
  margin: 0 0 10px;
  font-size: 32px;
}

.outline-checkbox {
  margin-bottom: 10px;
}

.outline-checkbox label {
  font-size: 18px;
}

#floating-input input[type="text"] {
  width: 100%;
  padding: 15px;
  font-size: 20px;
  border: 2px solid #ccc;
  border-radius: 12px;
  box-sizing: border-box;
}

.app-header {
  text-align: center;
  margin: 0;
  z-index: 1001;
  position: absolute;
  background-color: transparent;
  text-align: center;
  justify-content: center;
  width: 100%;
  background: linear-gradient(to bottom, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0));
  margin-bottom: 10px;
}

.app-header h1 {
  font-size: 2em;
  color: #fff;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
  margin-bottom: 5px;
}

.app-description {
  font-size: 1.2em;
  color: #fff;
  text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.8);
  max-width: 900px;
  margin-left: auto;
  margin-right: auto;
  line-height: 1.25;
  padding: 0 20px;
  margin-top: 5px;
}

#reload-button {
  position: absolute;
  top: 50%;
  right: 20px;
  transform: translateY(-50%);
  font-size: 50px;
  padding: 10px 25px;
  background-color: rgba(255, 255, 255, 0.6);
  color: black;
  border: none;
  border-radius: 50%;
  cursor: pointer;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

#reload-button:hover {
  background-color: rgba(255, 255, 255, 0.8);
}

.modal {
  display: flex;
  justify-content: center;
  align-items: center;
  position: fixed;
  z-index: 1;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: rgba(0, 0, 0, 0.4);
}

.modal-content {
  background-color: #fefefe;
  margin: auto;
  padding: 20px;
  border: 1px solid #888;
  width: 80%;
  max-width: 500px;
  border-radius: 10px;
  text-align: center;
}

.close {
  color: #aaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
}

.close:hover,
.close:focus {
  color: black;
  text-decoration: none;
  cursor: pointer;
}
</style>
r
