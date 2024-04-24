<template>

    <Head :title="title" />
    <app-layout>
        <div class="card">
            <!-- Header -->
            <div class="flex justify-content-between">

                <h2>{{ header }}</h2>
                <slot name="header"></slot>
            </div>
            <!-- Table of order items -->
            <DataTable :value="items">
                <template #header>
                    <div class="flex flex-wrap align-items-center justify-content-between gap-2">
                        <span class="text-xl text-900 font-bold">{{ header }}</span>
                    </div>
                </template>

                <Column field="code" header="Code" class="col-1"></Column>
                <Column field="name" header="Name" class="col-3"></Column>
                <Column field="quantity" header="Quantity" class="col-1"></Column>
                <Column field="parts" header="Parts" class="col-1"></Column>
                <Column field="discount" header="Discount" class="col-1"></Column>
                <Column field="selling_price" header="Selling Price" class="col-1"></Column>
                <Column field="expire_date" header="Exp. Date" class="col-2"></Column>
                <Column field="total_price" header="Total Price" class="col-1"></Column>


                <template #footer>
                    <div @keyup.ctrl.enter="submitItem" @keyup.enter.exact="getProduct" class="card p-3"
                        style="border-color: var(--primary-color)">

                        <div class="formgrid grid">
                            <div class="field col-2">
                                <label for="code">Code</label>
                                <InputText v-model="current.code" class="w-full" id="code" />
                            </div>
                            <div class="field col-3">
                                <label for="name">Name</label>
                                <InputText v-model="current.name" class="w-full" id="name" disabled />
                            </div>
                            <div class="field col-1 m-0">
                                <label for="quantity">Quantity</label>
                                <InputText v-model="current.quantity" class="w-full" id="quantity" />
                            </div>
                            <div class="field col-1">
                                <label for="parts">Parts</label>
                                <InputText v-model="current.parts" class="w-full" id="parts" />
                            </div>
                            <div class="field col-1">
                                <label for="discount">Discount</label>
                                <InputText v-model="current.discount" class="w-full" id="discount" />
                            </div>
                            <div class="field col-1">
                                <label for="selling-price">Selling Price</label>
                                <InputText v-model="current.sellingPrice" class="w-full" id="selling-price" />
                            </div>
                            <div class="field col-2">
                                <label for="exp-date">Exp. Date</label>
                                <InputText v-model="current.expDate" class="w-full" id="exp-date" disabled />
                            </div>
                            <div class="field col-1">
                                <label for="total-price">Total Price</label>
                                <InputText v-model="current.totalPrice" class="w-full" id="total-price" />
                            </div>
                        </div>
                    </div>
                </template>
            </DataTable>
            <!-- row form in table's footer to add new item -->
            <!-- Total Price -->
            <!-- Save and cancel buttons -->
        </div>
    </app-layout>
</template>


<script setup>
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { reactive } from 'vue';
import axios from 'axios';
const props = defineProps({
    title: String,
    header: String,
    items: Object
})

const current = reactive({
    code: '',
    name: '',
    quantity: 1,
    parts: 0,
    discount: 0,
    sellingPrice: 0,
    totalPrice: 0,
    expDate: ''
})

const getProduct = () => {
    console.log(current)
    axios.get('/orders/product/' + current.code)
        .then((response) => {
            const product = response.data.product
            current.name = product.name
            current.sellingPrice = product.price
            console.log(current)
        })
}

const submitItem = () => {
    console.log("Item Inserted")
}
</script>