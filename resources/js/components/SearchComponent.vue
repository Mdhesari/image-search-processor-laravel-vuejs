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

        <button :disabled="loading" @click="process"
                class="mx-2 px-4 py-2 text-sm rounded text-gray-800 border focus:outline-none hover:bg-gray-100">
            <span v-if="loading" role="status">
                <svg aria-hidden="true"
                     class="inline w-4 h-4 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600"
                     viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                        fill="currentColor"/>
                    <path
                        d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                        fill="currentFill"/>
                </svg>
                <span class="sr-only">Loading...</span>
            </span>
            <span v-if="!loading">Process</span>
        </button>
    </div>
    <gallery/>
</template>

<script>
export default {
    methods: {
        process() {
            this.loading = true
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
            }).finally(() => {
                this.loading = false
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
            width: null,
            height: null,
            success: false,
            successMessage: "",
            error: "",
            loading: false
        };
    },
};
</script>
