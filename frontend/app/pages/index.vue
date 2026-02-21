<script setup lang="ts">
import { gql } from '@apollo/client/core'

const GET_PRODUCTS = gql`
  query GetProducts($first: Int!) {
    products(first: $first) {
      id
      title
      handle
    }
  }
`

const { result, loading, error } = useQuery(GET_PRODUCTS, { first: 10 })

const products = computed(() => result.value?.products ?? [])
</script>

<template>
    <div style="padding: 2rem; font-family: sans-serif;">
        <h1>Shopify Products</h1>

        <div v-if="loading">Loading...</div>
        <div v-else-if="error">Error: {{ error.message }}</div>

        <ul v-else>
            <li v-for="product in products" :key="product.id">
                {{ product.title }}
            </li>
        </ul>
    </div>
</template>