<template>
    <div class="mb-4 px-2 w-full">
        <alert-success v-if="success" title="Successfully processed." :description="successMessage"></alert-success>
        <alert-failure v-if="error !== ''" :title="error"/>
        <div class="relative">
            <div class="absolute left-0 inset-y-0 pl-3 flex items-center">
                <svg class="fill-current h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path
                        d="M12.9 14.32a8 8 0 1 1 1.41-1.41l5.35 5.33-1.42 1.42-5.33-5.34zM8 14A6 6 0 1 0 8 2a6 6 0 0 0 0 12z"/>
                </svg>
            </div>

            <input
                v-model="query"
                class="w-full border pl-12 pr-4 py-2 rounded-full focus:border-blue-500 focus:shadow-outline outline-none"
                type="text" placeholder="Search..."/>
        </div>
    </div>
    <div class="inline-flex">
        <div class="select-none border py-2 px-4 cursor-pointer bg-gray-100 hover:bg-gray-200 rounded-l"
             @click="decrease">
            -
        </div>

        <input class="border p-2 text-center outline-none" type="text" v-model="count"/>

        <div class="select-none border py-2 px-4 cursor-pointer bg-gray-100 hover:bg-gray-200 rounded-r"
             @click="increase">
            +
        </div>

        <input v-model="width"
               class="w-full border ml-2 px-4 py-2 focus:border-blue-500 focus:shadow-outline outline-none outline-none"
               type="text" placeholder="Resize Width..."/>
        <input v-model="height"
               class="w-full border ml-2 px-4 py-2 focus:border-blue-500 focus:shadow-outline outline-none outline-none"
               type="text" placeholder="Resize Height..."/>

        <button @click="process"
                class="mx-2 px-4 py-2 text-sm rounded text-gray-800 border focus:outline-none hover:bg-gray-100">Process
        </button>
    </div>
    <gallery/>
</template>

<script>
export default {
    methods: {
        process() {
            this.error = ""
            axios.post('api/images/search-process', {
                query: this.query,
                count: this.count,
                width: this.width,
                height: this.height
            }).then(({data}) => {
                this.success = true
                this.successMessage = "Image search is processing your request."

                setTimeout(() => this.success = false, 5000)
            }).catch(({response}) => {
                if (response.data?.errors) {
                    let errors = response.data.errors
                    for (let err in errors) {
                        this.error += errors[err][0]
                    }
                } else {
                    alert('something went wrong!')
                }
                // response.data.errors.map
            })

            //TODO: trigger event to gallery for loading new images
        },
        increase() {
            this.count++;
        },
        decrease() {
            if (this.count > 1) {
                this.count--;
            }
        },
    },
    data() {
        return {
            query: '',
            count: 10,
            width: 1000,
            height: 1000,
            success: false,
            successMessage: "",
            error: ""
        };
    },
};
</script>
