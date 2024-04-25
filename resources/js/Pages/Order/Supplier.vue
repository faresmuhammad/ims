<template>

    <OrderLayout header="Supplier" :title="order ? order.reference_code : 'No Order'" :order="order">
        <template #header>
            <Dropdown v-model="selectedSupplier" :options="suppliers" placeholder="Select a Supplier"
                @update:modelValue="updateSupplierOfOrder" optionLabel="name" optionValue="id" />
            <h5>{{ order ? 'Order #' + order.reference_code : 'No Order' }}</h5>
            <!-- Show created since [time] -->

            <!-- Show updated since [time] -->
            <!-- Show a status tag to indicate completed or not -->
        </template>
    </OrderLayout>
</template>

<script setup>
import { router } from '@inertiajs/vue3';
import { onMounted, reactive, ref } from 'vue';
import OrderLayout from '../../Layouts/OrderLayout.vue';
import axios from 'axios';

const props = defineProps({
    suppliers: Object,
});

const selectedSupplier = ref(null)
const order = ref(null)
onMounted(() => {
    axios.post('/orders/supplier', {})
        .then((response) => {
            console.log(response.data)
            order.value = response.data
        })
})

const updateSupplierOfOrder = () => {
    router.put('/orders/' + order.value.id, {
        supplier_id: selectedSupplier.value
    }, {
        onSuccess: (page) => {
            console.log(page) 
        }
    })
}
</script>