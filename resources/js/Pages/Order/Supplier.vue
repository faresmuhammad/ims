                <!-- Show created since [time] -->
                <template>
    <OrderLayout title="Supplier" header="Supplier">
        <template #header>
            <Dropdown v-model="selectedSupplier" :options="suppliers" editable placeholder="Select a Supplier"
                @change="updateSupplierOfOrder" optionLabel="name" optionValue="id" />
                <!-- Show updated since [time] -->
                <!-- Show a status tag to indicate completed or not -->
        </template>
    </OrderLayout>
</template>

<script setup>
import { router } from '@inertiajs/vue3';
import { onMounted, reactive, ref } from 'vue';
import OrderLayout from '../../Layouts/OrderLayout.vue';

const props = defineProps({
    suppliers: Object,
    order: Object
});

const selectedSupplier = ref(null)

onMounted(() => {
    router.post('/orders/supplier', {}, {
        onSuccess: (page) => {
            console.log(page)
        }
    })
})

const updateSupplierOfOrder = () => {
    // router.put('/orders/'+ )
}
</script>