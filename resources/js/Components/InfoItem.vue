<template>
    <li class="flex align-items-center py-3 px-2 flex-warp surface-overlay">
        <div class="text-500 w-full md:w-2 font-medium">{{label}}</div>
        <div class="text-500 w-full md:w-10">{{ value }}</div>
    </li>
</template>

<script setup>

defineProps({
    label: String,
    value: String
})
</script>