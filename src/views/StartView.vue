<template>
    <div class="start-view">
      <h1>Welcome to the Game!</h1>
      <form @submit.prevent="submitUsername">
        <input
          type="text"
          v-model="username"
          placeholder="Enter your username"
          class="username-input"
        />
        <button type="submit" class="submit-button">Start</button>
      </form>
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
      submitUsername() {
        if (this.username.trim()) {
          alert(`Welcome, ${this.username}!`);
          // Here, you could route to the next view or emit an event.
        } else {
          alert("Please enter a username!");
        }
      },
    },
  };
  </script>
  
  <style scoped>
  .start-view {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100vh;
    background-color: #f3f4f6;
  }
  
  h1 {
    font-size: 2rem;
    margin-bottom: 1rem;
  }
  
  form {
    display: flex;
    flex-direction: column;
    align-items: center;
  }
  
  .username-input {
    padding: 0.5rem;
    font-size: 1rem;
    margin-bottom: 1rem;
    border: 1px solid #ccc;
    border-radius: 4px;
  }
  
  .submit-button {
    padding: 0.5rem 1rem;
    font-size: 1rem;
    color: #fff;
    background-color: #007bff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }
  
  .submit-button:hover {
    background-color: #0056b3;
  }
  </style>