<template>
    <div class="my-5 h-2 overflow-hidden rounded-full bg-gray-200">
        <div class="h-2 animate-pulse rounded-full bg-gray-800" :style="{width: progress+'%'}">
            <div class="h-full w-full translate-x-full transform bg-white"></div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        progress: Number,
    }
}
</script>
