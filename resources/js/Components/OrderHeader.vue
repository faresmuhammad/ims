<template>
    <div class="grid justify-content-between">
        <div class="col-3">
            <h2>{{title}}</h2>
            <Dropdown
                v-if="suppliers"
                v-model="selectedSupplier"
                :options="suppliers"
                placeholder="Select a Supplier"
                @change="emit('update:supplier', $event.value)"
                optionLabel="name"
                optionValue="id"
            />
        </div>

        <div class="grid col-9">
            <div class="col-10 flex flex-column justify-content-between">
                <h5>
                    {{ order ? "Order #" + order.reference_code : "No Order" }}
                </h5>
                <div class="flex gap-2">
                    <span>Created at:</span>
                    <Inplace>
                        <template #display>
                            {{ formattedTimeSince }}
                        </template>
                        <template #content>
                            {{ formatDateTime(order.created_at) }}
                        </template>
                    </Inplace>
                </div>
            </div>

            <div class="col-2">
                <Tag
                    :value="order.completed ? 'Completed' : 'Incomplete'"
                    :severity="order.completed ? 'success' : 'warning'"
                    class="text-xl py-1 px-3"
                    :icon="
                        order.completed
                            ? 'pi pi-check text-xl'
                            : 'pi pi-times text-xl'
                    "
                />
            </div>
        </div>
    </div>
</template>

<script setup>
import {formatDateTime} from '@/helpers';
import {useOrders} from '@/composables/orders';

const props = defineProps({
    order: Object,
    title:String,
    suppliers: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(["update:supplier"]);
const selectedSupplier = defineModel("selectedSupplier");

const { formattedTimeSince, updateTodayTimeSince} = useOrders(props.order)


updateTodayTimeSince()
</script>
