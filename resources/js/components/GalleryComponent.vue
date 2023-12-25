<template>
    <div class="container mx-auto mt-4">
        <div>
            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                <div class="bg-blue-600 h-2.5 rounded-full" style="width: 45%"></div>
            </div>
        </div>
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
        this.getImages()

        setInterval(() => {
            this.getImages();
        }, 10000)
    },
    methods: {
        getImages() {
            axios.get('/api/images').then(({data}) => {
                if (data.data.length > this.images)
                    this.images = data.data
            })
        },
    },
    data() {
        return {
            images: [],
            progress: 0
        };
    },
};
</script>
