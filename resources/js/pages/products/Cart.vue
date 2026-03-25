<script setup lang="ts">
import { ref } from "vue"

const cart = ref(JSON.parse(localStorage.getItem("cart") || "[]"))

const removeItem = (id: number) => {
  cart.value = cart.value.filter((item: any) => item.id !== id)
  localStorage.setItem("cart", JSON.stringify(cart.value))
}

const updateQuantity = (item: any, amount: number) => {
  item.quantity += amount
  if (item.quantity <= 0) removeItem(item.id)

  localStorage.setItem("cart", JSON.stringify(cart.value))
}
</script>

<template>
  <div>
    <h2>Shopping Cart</h2>

    <div v-for="item in cart" :key="item.id">
      <h3>{{ item.name }}</h3>
      <p>{{ item.price }} €</p>

      <button @click="updateQuantity(item, -1)">-</button>
      {{ item.quantity }}
      <button @click="updateQuantity(item, 1)">+</button>

      <button @click="removeItem(item.id)">Remove</button>
    </div>
  </div>
</template>