<template>

    <Head title="Login" />
    <div class="flex align-content-center justify-content-center">
        <div class="surface-card p-4 shadow-2 border-round w-full lg:w-6">
            <div class="text-center mb-5">
                <img src="https://blocks.primevue.org/images/blocks/logos/hyper.svg" alt="Image" height="50"
                    class="mb-3" />
                <div class="text-900 text-3xl font-medium mb-3">Inventory Management System</div>
                <div class="text-900 text-3xl font-medium mb-3">Login</div>
            </div>

            <form @submit.prevent>
                <div class="flex align-items-center gap-2">
                    <label for="username" class="block text-900 font-medium mb-3 w-2">Username</label>
                    <InputText id="username" type="text" class="w-6 mb-3" v-model="form.username" />
                    <InlineMessage class="mb-3 w-3" v-if="!form.username">Username is required</InlineMessage>
                </div>

                <div class="flex align-items-center gap-2">
                    <label for="password" class="block text-900 font-medium mb-2 w-2">Password</label>
                    <Password id="password" class="mb-3 w-6" v-model="form.password" toggleMask />
                </div>


                <Button @click="register" label="Login" class="w-full text-2xl"></Button>
            </form>
        </div>
    </div>
</template>
//TODO: Send Form Data with validation
//TODO: Show feedback to user
//TODO: Redirect to home page


<script setup>
import { reactive } from 'vue';


const form = reactive({
    username: '',
    password: ''
})

//TODO: set error message depending on the feedback from the server
//Use cases
//incorrect username or password
const error = reactive({
    username: '',
    password: ''
})
</script>