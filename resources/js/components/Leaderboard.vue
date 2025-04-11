<template>
    <div class="p-4 max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-4">Leaderboard</h1>
        <div class="flex mb-4 gap-2">
            <input v-model="search" placeholder="Search by name..." @input="fetchUsers" class="border rounded p-2 flex-1" />
            <button @click="createUser" class="bg-green-500 text-white p-2 rounded">+ Add User</button>
        </div>
        <table class="w-full border">
            <thead>
            <tr>
                <th @click="toggleSort('name')" class="cursor-pointer">Name</th>
                <th>Age</th>
                <th>Address</th>
                <th @click="toggleSort('points')" class="cursor-pointer">Points</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="user in users" :key="user.id">
                <td @click="selectUser(user)" class="text-blue-600 cursor-pointer">{{ user.name }}</td>
                <td>{{ user.age }}</td>
                <td>{{ user.address }}</td>
                <td>{{ user.points }}</td>
                <td class="space-x-2">
                    <button @click="increment(user.id)" class="bg-blue-500 text-white px-2 rounded">+</button>
                    <button @click="decrement(user.id)" class="bg-yellow-500 text-white px-2 rounded">-</button>
                    <button @click="deleteUser(user.id)" class="bg-red-500 text-white px-2 rounded">x</button>
                </td>
            </tr>
            </tbody>
        </table>

        <div v-if="selectedUser" class="mt-4 p-4 border rounded">
            <h2 class="text-xl font-semibold mb-2">User Details</h2>
            <p><strong>Name:</strong> {{ selectedUser.name }}</p>
            <p><strong>Age:</strong> {{ selectedUser.age }}</p>
            <p><strong>Points:</strong> {{ selectedUser.points }}</p>
            <p><strong>Address:</strong> {{ selectedUser.address }}</p>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const users = ref([])
const selectedUser = ref(null)
const search = ref('')
const sortBy = ref('')
const sortOrder = ref('asc')

const fetchUsers = async () => {
    const res = await axios.get('/api/users', {
        params: { search: search.value, sortBy: sortBy.value, sortOrder: sortOrder.value }
    })
    users.value = res.data
}

const increment = async (id) => {
    await axios.patch(`/api/users/${id}/increment`)
    fetchUsers()
}

const decrement = async (id) => {
    await axios.patch(`/api/users/${id}/decrement`)
    fetchUsers()
}

const deleteUser = async (id) => {
    await axios.delete(`/api/users/${id}`)
    fetchUsers()
}

const selectUser = async (user) => {
    const res = await axios.get(`/api/users/${user.id}`)
    selectedUser.value = res.data
}

const createUser = async () => {
    const name = prompt('Name?')
    const age = prompt('Age?')
    const address = prompt('Address?')
    if (name && age && address) {
        await axios.post('/api/users', { name, age, address })
        fetchUsers()
    }
}

const toggleSort = (field) => {
    if (sortBy.value === field) {
        sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc'
    } else {
        sortBy.value = field
        sortOrder.value = 'asc'
    }
    fetchUsers()
}

onMounted(fetchUsers)
</script>

<style scoped>
th {
    background: #f1f1f1;
    padding: 8px;
}
td {
    padding: 8px;
    border-top: 1px solid #ddd;
}
</style>
