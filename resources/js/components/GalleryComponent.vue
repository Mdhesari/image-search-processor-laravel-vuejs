<template>
    <div class="container mx-auto">
        <h2 class="py-4 text-3xl font-bold text-center text-indigo-600">
            Gallery
        </h2>
        <div class="lg:gap-2 lg:grid lg:grid-cols-3">
            <div class="w-full rounded" v-for="image in images" :key="image.id">
                <img :src="image.image" alt="image"/>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    mounted() {
        console.log('hi')
        axios.get('/api/images').then(({data}) => {
            console.log(data)
            this.images = data.data
        })
    },
    data() {
        return {
            images: [],
        };
    },
};
</script>
