<template>
    <div class="relative mb-5 h-2 rounded-full bg-gray-200">
        <div class="h-2 rounded-full bg-red-500" :style="{width: progress+'%'}"></div>
        <span
            class="absolute inset-0 flex items-center justify-center text-sm font-medium text-gray-900">{{ progress }}%</span>
    </div>
</template>

<script>
export default {
    props: {
        progress: Number,
    }
}
</script>
