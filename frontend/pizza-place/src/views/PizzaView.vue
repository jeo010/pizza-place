<template>
  <div class="flex flex-col gap-4 w-full">
    <div class="flex flex-row gap-4">
        <input
            ref="fileInput"
            type="file"
            accept=".csv"
            style="display: none"
            @change="handleFileUpload"
        />
        <button
            @click="triggerFileInput"
            class="p-4 border-3 border-blue-300 text-blue-300 hover:bg-blue-300 hover:text-white rounded-md w-full md:w-1/4"
        >
            Upload CSV
        </button>
        <router-link
            :to="{ name: 'orders'}"
            class="p-4 border-3 border-blue-300 text-blue-300 hover:bg-blue-300 hover:text-white rounded-md w-full md:w-1/4 flex items-center justify-center"
        >
            Go to Dashboard
        </router-link>
    </div>
    <div class="flex flex-col md:flex-row gap-4 items-center w-full">
        <!-- Search by name -->
        <!-- No extra code needed here for search box functionality -->
        <input
            v-model="searchQuery"
            type="text"
            placeholder="Search by pizza name"
            class="border border-gray-300 rounded-md px-3 py-2 w-full md:w-1/3"
        />

        <!-- Filter by category -->
        <select
            v-model="selectedCategory"
            class="border border-gray-300 rounded-md px-3 py-2 w-full md:w-1/4"
        >
            <option value="">All Categories</option>
            <option v-for="cat in uniqueCategories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
        </select>

        <!-- Filter by ingredient -->
        <select
            v-model="selectedIngredients"
            multiple
            class="border border-gray-300 rounded-md px-3 py-2 w-full md:w-1/4"
        >
            <option v-for="ing in uniqueIngredients" :key="ing.id" :value="ing.id">{{ ing.name }}</option>
        </select>
        
    </div>
    <table class="w-full hidden md:block">
        <thead>
            <tr class="border-b-1 border-b border-gray-300">
                <th scope="col" class="px-3 py-3.5 text-left text-gray-500 w-1/8">
                    NAME
                </th>
                <th scope="col" class="px-3 py-3.5 text-left text-gray-500 w-1/8">
                    CATEGORY
                </th>
                <th scope="col" class="px-3 py-3.5 text-left text-gray-500 max-w-4/8">
                    INGRIDIENTS
                </th>
                <th scope="col" class="px-3 py-3.5 text-left text-gray-500 w-1/8">
                    SIZES
                </th>
                <th scope="col" class="px-3 py-3.5 text-left text-gray-500 w-1/8">
                    PRICES
                </th>
            </tr>
        </thead>
        <tbody class="divide-y-gray-700 divide-y">
            <tr v-for="pizza in filteredPizzas" :key="pizza.id" class="hover:bg-gray-100">
                <td class="px-3 py-3.5 text-gray-700">{{ pizza.name }}</td>
                <td class="px-3 py-3.5 text-gray-700">{{ pizza.category.name }}</td>
                <td class="px-3 py-3.5 text-gray-700 flex flex-row gap-1 flex-wrap">
                    <span class="bg-blue-300 text-white text-center flex items-center justify-center py-2 px-4 rounded-full text-xs" v-for="ingredient in pizza.ingredients" :key="ingredient.id">
                        {{ ingredient.name }}
                    </span>
                </td>
                <td class="px-3 py-3.5 text-gray-700">
                    <ul>
                        <li v-for="size in pizza.pizzas" :key="size.id">
                            {{ size.size }}
                        </li>
                    </ul>
                </td>
                <td class="px-3 py-3.5 text-gray-700">
                    <ul>
                        <li v-for="price in pizza.pizzas" :key="price.id">
                            {{ price.price }}$
                        </li>
                    </ul>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="block md:hidden">
        <div class="flex flex-col gap-4">
            <div v-for="pizza in filteredPizzas" :key="pizza.id" class="border p-4 rounded-md shadow-md">
                <h2 class="text-lg font-semibold">{{ pizza.name }}</h2>
                <p class="text-gray-600">Category: {{ pizza.category.name }}</p>
                <p class="text-gray-600">Ingredients:</p>
                <div class="flex flex-row gap-1 flex-wrap">
                <span class="bg-blue-300 text-white text-center flex items-center justify-center py-2 px-4 rounded-full text-xs" v-for="ingredient in pizza.ingredients" :key="ingredient.id">
                    {{ ingredient.name }}
                </span>
                </div>
                <p class="text-gray-600">Sizes:</p>
                <ul class="list-disc pl-5">
                    <li v-for="size in pizza.pizzas" :key="size.id">{{ size.size }}: {{ size.price }}</li>
                    
                </ul>
                
            </div>
        </div>
    </div>

    
  </div>
</template>


<script>
    import axios from "axios";

    export default {
        name: "pizzas",
        data(){
            return {
                uniqueCategories : [],
                uniqueIngredients: [],
                pizzas: [],
                searchQuery: "",
                selectedCategory: "",
                selectedIngredients: "",
            }
        },
        computed: {
            filteredPizzas() {
                return this.pizzas.filter(pizza => {
                    return (
                        (!this.searchQuery || pizza.name.toLowerCase().includes(this.searchQuery.toLowerCase())) &&
                        (!this.selectedCategory || pizza.category.id === this.selectedCategory) &&
                        (!this.selectedIngredients.length || 
                            this.selectedIngredients.every(selectedIng => 
                                pizza.ingredients.some(ing => ing.id === selectedIng)
                            )
                        )&&
                        (!this.selectedSize || pizza.pizzas.some(size => size.size === this.selectedSize))
                    );
                });
            }

        },
        mounted() {

            this.getPizzas();
        },
        methods:{
            getPizzas(){
                axios.get('http://pizza-place-api.test/api/pizzas').then(

                    response => {
                        console.log(response.data);
                        this.pizzas = response.data[0];
                        this.uniqueCategories = response.data[1];
                        this.uniqueIngredients = response.data[2];
                    }

                ).catch(
                    error => {
                        console.error("There was an error fetching the pizza information: ", error);
                    }

                )
            }
        }
    }
</script>


<script setup>
    import { ref } from "vue";

    const fileInput = ref(null);

    function triggerFileInput() {
        fileInput.value.click();
    }

    function handleFileUpload(event) {
        const file = event.target.files[0];
        if (file && file.type === "text/csv") {
            // Handle the CSV file upload logic here
            // For now, just log the file name
            console.log("Selected file:", file.name);

            const formData = new FormData();
            formData.append("file", file);

            fetch("http://pizza-place-api.test/api/pizzas", {
                method: "POST",
                body: formData,
            })
            .then((response) => {
                if (!response.ok) {
                    throw new Error("Upload failed");
                }
                return response.json();
            })
            .then((data) => {
                console.log("Upload successful:", data);
                alert("CSV uploaded successfully!");
            })
            .catch((error) => {
                console.error("Error uploading CSV:", error);
                alert("Failed to upload CSV.");
            });
        } else {
            alert("Please select a valid CSV file.");
        }
    }
</script>

<style scoped>
.upload-csv {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
}
button {
  padding: 0.5rem 1.5rem;
  font-size: 1rem;
  cursor: pointer;
}
</style>
