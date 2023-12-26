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
    props: {
        count: Number
    },
    mounted() {
        this.getImages()

        setInterval(() => {
            this.getImages();
        }, 10000)
    },
    methods: {
        getImages() {
            axios.get('/api/images', {
                params: {
                    per_page: this.count
                }
            }).then(({data}) => {
                data = data.data
                if (data.length && this.images.length) {
                    let last = data[data.length - 1]
                    if (last['id'] !== this.images[this.images.length - 1]['id']) {
                        this.images = data
                    }
                } else if (data.length) {
                    this.images = data
                }
            })
        },
    },
    data() {
        return {
            images: [],
        };
    },
};
</script>
