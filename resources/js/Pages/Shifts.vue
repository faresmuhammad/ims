<template>

    <Head title="Shifts" />
    <app-layout>

        <div class="card">
            <!-- Start and Stop Shift buttons -->
            <div class="flex justify-content-end">
                <Button v-if="activeShift" label="End Shift" severity="danger" @click="endShift" />
                <Button v-else label="Start Shift" @click="startShift" />
            </div>
            <DataTable :value="shifts.data" showGridlines stripedRows editMode="cell"
                @cell-edit-init="(event) => { if (event.field === 'realAmount') moneyCalcDialog = true; }"
                @cell-edit-complete="updateShift" :rowClass="activeRow">
                <Column field="startDate" header="Start Date" class="text-center" />
                <Column field="startTime" header="Start Time" class="text-center" />
                <Column field="endDate" header="End Date" class="text-center" />
                <Column field="endTime" header="End Time" class="text-center" />
                <Column field="expectedAmount" header="Expected Amount" class="text-center">
                    <template #body="{ data, field }">
                        <!-- format this to money format -->
                        {{ data[field] }}
                    </template>

                </Column>
                <Column field="realAmount" header="Real Amount" class="text-center">
                    <template #body="{ data, field }">
                        <!-- format this to money format -->
                        {{ data[field] }}
                    </template>
                    <template #editor="{ data, field }">
                        {{ data[field] }}
                    </template>
                </Column>
                <Column field="difference" header="Difference" class="text-center" />
                <Column field="user" header="User" class="text-center" />
            </DataTable>

            <!-- TODO: Need more work -->
            <Dialog v-model:visible="moneyCalcDialog" header="Money Calculator" :style="{ width: '450px' }"
                class="p-fluid">
                <div class="field">
                    <label for="m200">200</label>
                    <InputText id="m200" v-model="moneyCalculator.m200" autofocus />
                </div>
                <div class="field">
                    <label for="m100">100</label>
                    <InputText id="m100" v-model="moneyCalculator.m100" autofocus />
                </div>
                <div class="field">
                    <label for="m50">50</label>
                    <InputText id="m50" v-model="moneyCalculator.m50" autofocus />
                </div>
                <div class="field">
                    <label for="m20">20</label>
                    <InputText id="m20" v-model="moneyCalculator.m20" autofocus />
                </div>
                <div class="field">
                    <label for="m10">10</label>
                    <InputText id="m10" v-model="moneyCalculator.m10" autofocus />
                </div>
                <div class="field">
                    <label for="m5">5</label>
                    <InputText id="m5" v-model="moneyCalculator.m5" autofocus />
                </div>
                <div class="field">
                    <label for="m1">1</label>
                    <InputText id="m1" v-model="moneyCalculator.m1" autofocus />
                </div>


                <template #footer>
                    <Button label="Cancel" icon="pi pi-times" outlined @click="moneyCalcDialog = false" />
                    <Button label="Save" icon="pi pi-check" @click="updateRealAmount" />
                </template>
            </Dialog>
        </div>
    </app-layout>
</template>

<script setup>
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { reactive, ref } from 'vue';

const props = defineProps({
    shifts: Object,
    activeShift: Object
})

const moneyCalcDialog = ref(false)
const moneyCalculator = reactive({
    m200: 0,
    m100: 0,
    m50: 0,
    m20: 0,
    m10: 0,
    m5: 0,
    m1: 0,
})

const activeRow = (data) => {
    return [{ 'bg-primary': data.endDate == null }]
}

const startShift = () => {
    router.post('/shift/start', {}, {
        onSuccess: (page) => {
            console.log(page);
        }
    })
}

const endShift = () => {

    console.log(new Date().toISOString().slice(0, 19).replace('T', ' '));
    router.put('/shift/end', {}, {
        onSuccess: (page) => {
            console.log(page);
        }
    })
}

//TODO: Need more work 
const updateRealAmount = () => {
    const { m200, m100, m50, m20, m10, m5, m1 } = moneyCalculator
    const total = 200 * m200 + 100 * m100 + 50 * m50 + 20 * m20 + 10 * m10 + 5 * m5 + 1 * m1;
    console.log(moneyCalculator,total);
}

const updateShift = (event) => {

    console.log(event);

    //in case the shift is running
    //update the start date and time only
    
    //in case the shift is over
    //updating start or end date -> recalculate the expected amount and update the difference
    //updating real amount -> update the difference

    //update the user
}

</script>