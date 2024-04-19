<template>

    <Head title="Login" />
    <div class="surface-ground flex align-items-center justify-content-center min-h-screen min-w-screen overflow-hidden">
        <div class="flex flex-column align-items-center justify-content-center">
            <img :src="logoUrl" alt="Sakai logo" class="mb-5 w-6rem flex-shrink-0" />
            <div
                style="border-radius: 56px; padding: 0.3rem; background: linear-gradient(180deg, var(--primary-color) 10%, rgba(33, 150, 243, 0) 30%)">
                <div class="w-full surface-card py-8 px-5 sm:px-8" style="border-radius: 53px">
                    <div class="text-center mb-5">
                        <div class="text-900 text-3xl font-medium mb-3">Inventory Management System</div>
                        <span class="text-600 font-medium">Sign in to continue</span>
                    </div>

                    <div class="flex flex-column gap-4">
                        <div class="flex flex-column">

                            <label for="username" class="block text-900 text-xl font-medium mb-2">Username</label>
                            <InputText id="username" type="text" class="w-full md:w-30rem " style="padding: 1rem"
                                v-model="form.username" />
                            <small class="p-error" v-if="error.username">{{ error.username }}</small>
                        </div>


                        <div class="flex flex-column">
                            <label for="password1" class="block text-900 font-medium text-xl mb-2">Password</label>
                            <Password id="password1" v-model="password" placeholder="Password" :toggleMask="true"
                                class="w-full mb-3" inputClass="w-full" :inputStyle="{ padding: '1rem' }"></Password>
                            <small class="p-error" v-if="error.password">{{ error.password }}</small>
                        </div>
                        <div class="flex align-items-center justify-content-between mb-5 gap-5">
                            <div class="flex align-items-center">
                                <Checkbox v-model="form.remember" id="remember" binary class="mr-2"></Checkbox>
                                <label for="remember">Remember me</label>
                            </div>
                            <a class="font-medium no-underline ml-2 text-right cursor-pointer"
                                style="color: var(--primary-color)">Forgot password?</a>
                        </div>
                        <Button @click="login" label="Login" class="w-full p-3 text-xl"></Button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- <div class="flex align-content-center justify-content-center">
        <div class="surface-card p-4 shadow-2 border-round w-full lg:w-6">
            <div class="text-center mb-5">
                <img src="https://blocks.primevue.org/images/blocks/logos/hyper.svg" alt="Image" height="50"
                    class="mb-3" />
                <div class="text-900 text-3xl font-medium mb-3">Inventory Management System</div>
                <div class="text-900 text-3xl font-medium mb-3">Login</div>
            </div>

            <form @submit.prevent>
                <div class="flex align-items-center gap-2">
                    <label for="username" class="block text-900 text-xl font-medium mb-2">Username</label>
                    <InputText id="username" type="text" class="w-full md:w-30rem mb-5"
                            style="padding: 1rem" v-model="form.username" />
                    <InlineMessage class="mb-3 w-3" v-if="!form.username">Username is required</InlineMessage>
                </div>

                <div class="flex align-items-center gap-2">
                    <label for="password" class="block text-900 font-medium mb-2 w-2">Password</label>
                    <Password id="password" class="mb-3 w-6" v-model="form.password" toggleMask />
                </div>


                <Button @click="register" label="Login" class="w-full text-2xl"></Button>
            </form>
        </div> 
    </div> -->
</template>
//TODO: Send Form Data with validation
//TODO: Show feedback to user
//TODO: Redirect to home page


<script setup>
import { reactive } from 'vue';
import { Head } from '@inertiajs/vue3';


const form = reactive({
    username: '',
    password: '',
    remember: true
})

//TODO: set error message depending on the feedback from the server
//Use cases
//incorrect username or password
const error = reactive({
    username: '',
    password: ''
})


const signin = () => {

}
</script>