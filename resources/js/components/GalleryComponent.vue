<template>
    <div class="container mx-auto mt-4">
        <h2 class="py-4 text-3xl font-bold text-center text-indigo-600">
            Gallery
        </h2>
        <div class="lg:gap-2 lg:grid lg:grid-cols-3">
            <div class="w-full rounded" v-for="image in images" :key="image.id">
                <img v-if="this.images.length" :src="image.image" alt="image"/>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    mounted() {
        axios.get('/api/images').then(({data}) => {
            this.images = data.data
        })
    },
    data() {
        return {
            images: [],
        };
    }
};
</script>
