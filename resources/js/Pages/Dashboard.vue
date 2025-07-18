<!-- resources/js/Pages/Dashboard.vue -->
<template>
    <DashboardLayout>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-8">

            <!-- Attractions -->
            <Card icon="📍" title="Attractions" color="bg-gradient-to-r from-pink-500 to-yellow-500" link="/attraction"
                as="button" />

            <!-- Map -->
            <Card icon="🗺️" title="Map" color="bg-gradient-to-r from-green-400 to-blue-500" link="/attractionsmap/map"
                as="button" />

            <!-- Profile -->
            <Card icon="👤" title="Profile" color="bg-gradient-to-r from-blue-500 to-cyan-500" link="/profile"
                as="button" />

            <!-- Logout -->
            <Card icon="🚪" title="Logout" color="bg-gradient-to-r from-red-500 to-pink-500"  link="/logout"
                method="post" as="button" />

        </div>
    </DashboardLayout>
</template>

<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import Card from '@/Components/DashboardCard.vue'
</script>
