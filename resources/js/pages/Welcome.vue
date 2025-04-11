// Directory: resources/js/components/Leaderboard.vue
<template>
    <div class="max-w-xl mx-auto p-4 border rounded">
        <div v-if="errorMessage" class="text-red-500 mb-4">{{ errorMessage }}</div>

        <!-- Search Bar -->
        <div class="mb-4">
            <input
                v-model="search"
                type="text"
                placeholder="Search by name"
                class="border px-3 py-1 rounded w-full"
            />
        </div>

        <!-- Sort Controls -->
        <div class="flex justify-between mb-2 font-bold">
            <div @click="sortBy('name')" class="cursor-pointer">Name</div>
            <div @click="sortBy('points')" class="cursor-pointer">Points</div>
        </div>

        <div v-for="user in filteredAndSortedUsers" :key="user.id" class="flex items-center justify-between mb-2">
            <div class="flex items-center space-x-2">
                <button @click="deleteUser(user.id)" class="border px-3 py-1 font-bold">X</button>
                <span @click="selectUser(user)" class="font-bold cursor-pointer">{{ user.name }}</span>
                <button @click="increment(user.id)" class="border px-3 py-1">+</button>
                <button @click="decrement(user.id)" class="border px-3 py-1">-</button>
            </div>
            <div>{{ user.points }} points</div>
        </div>
        <div class="flex justify-end">
            <button @click="showModal = true" class="mt-4 border px-4 py-2 rounded">+ Add User</button>
        </div>

        <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center">
            <div class="border-4 p-6 rounded w-96 relative">
                <h2 class="text-lg font-semibold mb-4">Add New User</h2>

                <div class="mb-2">
                    <label class="block text-sm font-medium">Name:</label>
                    <input v-model="form.name" type="text" class="border w-full px-2 py-1 rounded" />
                </div>
                <div class="mb-2">
                    <label class="block text-sm font-medium">Age:</label>
                    <input v-model="form.age" type="number" class="border w-full px-2 py-1 rounded" />
                </div>
                <div class="mb-2">
                    <label class="block text-sm font-medium">Email:</label>
                    <input v-model="form.email" type="email" class="border w-full px-2 py-1 rounded" />
                </div>
                <div class="mb-2">
                    <label class="block text-sm font-medium">Address:</label>
                    <input v-model="form.address" type="text" class="border w-full px-2 py-1 rounded" />
                </div>

                <div v-if="errorMessage" class="text-red-500 text-sm mb-2">{{ errorMessage }}</div>

                <div class="flex justify-end space-x-2 mt-4">
                    <button @click="showModal = false" class="px-4 py-1 border rounded">Cancel</button>
                    <button @click="createUser" class="px-4 py-1 bg-blue-600 text-white rounded">Save</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const users = ref([])
const selectedUser = ref(null)
const errorMessage = ref('')
const showModal = ref(false)
const search = ref('')
const form = ref({ name: '', age: '', email: '', address: '' })

const sortField = ref('points')
const sortDirection = ref('desc')

const filteredAndSortedUsers = computed(() => {
    return [...users.value]
        .filter((u) => u.name.toLowerCase().includes(search.value.toLowerCase()))
        .sort((a, b) => {
            const field = sortField.value
            const dir = sortDirection.value === 'desc' ? 1 : -1
            if (field === 'name') {
                return a.name.localeCompare(b.name) * dir
            } else if (field === 'points') {
                return (a.points - b.points) * -dir // reverse order for descending by default
            }
            return 0
        })
})

const sortBy = (field) => {
    if (sortField.value === field) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
    } else {
        sortField.value = field
        sortDirection.value = field === 'points' ? 'desc' : 'asc'
    }
}

const fetchUsers = async () => {
    const res = await axios.get('/api/users')
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
    alert(`Name: ${res.data.name}\nAge: ${res.data.age}\nPoints: ${res.data.points}\nAddress: ${res.data.address}`)
}

const createUser = async () => {
    errorMessage.value = ''
    try {
        await axios.post('/api/users', form.value)
        fetchUsers()
        showModal.value = false
        form.value = { name: '', age: '', email: '', address: '' }
    } catch (error) {
        if (error.response && error.response.data) {
            if (error.response.data.message) {
                errorMessage.value = error.response.data.message
            } else if (error.response.data.errors) {
                const firstKey = Object.keys(error.response.data.errors)[0]
                errorMessage.value = error.response.data.errors[firstKey][0]
            }
        } else {
            errorMessage.value = 'Something went wrong.'
        }
    }
}

onMounted(fetchUsers)
</script>

<style scoped>
button {
    border-radius: 6px;
}
</style>
