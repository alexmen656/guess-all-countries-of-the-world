import axios from "axios";

const instance = axios.create({
  baseURL: "https://alex.polan.sk/name-the-countries/",
  //timeout: 10000,
});

export default instance;