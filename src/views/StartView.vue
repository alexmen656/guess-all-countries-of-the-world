<template>
  <div class="start-view">
    <div class="container">
      <h1 class="welcome-text">Welcome to the Game!</h1>
      <form @submit.prevent="submitUsername" class="form-container">
        <input
          type="text"
          v-model="username"
          placeholder="Enter your username"
          class="username-input"
        />
        <button type="submit" class="submit-button">Start</button>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      username: "",
    };
  },
  methods: {
    async submitUsername() {
      if (this.username.trim()) {
        try {
          const response = await this.$axios.post("leaderboard.php", {
            action: "register",
            name: this.username,
          });

          if (response.data.success) {
            localStorage.setItem("verification_id", response.data.uuid);
            localStorage.setItem("username", this.username);
            this.$router.push("/game");
          } else {
            alert(`Error: ${response.data.error}`);
          }
        } catch (error) {
          alert("An error occurred while registering. Please try again.");
        }
      } else {
        alert("Please enter a username!");
      }
    },
  },
};
</script>

<style>
.start-view {
  position: relative;
  background-image: url("@/assets/bg.png");
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  height: 100vh;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;
  color: white;
  overflow: hidden;
}

.start-view::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent overlay */
  z-index: 1;
}

.start-view > * {
  position: relative;
  z-index: 2;
}

.welcome-text {
  padding: 10px 20px;
  font-size: 36px;
  margin-bottom: 20px;
}

.form-container {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.username-input,
.submit-button {
  width: 400px;
  padding: 10px;
  margin: 10px 0;
  border-radius: 4px;
  border: none;
}

.username-input {
  font-size: 16px;
  margin-bottom: 0;
}

.submit-button {
  background-color: #007bff;
  color: white;
  font-size: 16px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.submit-button:hover {
  background-color: #0056b3;
}

.container {
  background-color: rgba(0, 0, 0, 0.5);
  border-radius: 32px;
  padding: 20px;
}
</style>
