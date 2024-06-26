<template>
    <Head :title="product.name"/>
    <app-layout>
        <div class="card h-full">
            <div class="flex justify-content-between">
                <h2>{{ product.name }}</h2>
                <div>
                    <Button
                        v-if="can('edit product')"
                        icon="pi pi-pencil"
                        rounded
                        class="w-2.5rem h-2.5rem p-0 mr-2"
                        @click="editDialog = true"
                    />
                    <Button
                        v-if="can('delete product')"
                        icon="pi pi-trash"
                        rounded
                        class="w-2.5rem h-2.5rem p-0 p-button-danger"
                        @click="deleteConfirmation = true"
                    />
                </div>
            </div>
            <TabView>
                <TabPanel header="Product Information">
                    <!-- TODO: Show Product Details code, name, category, description, price, stockoffset -->

                    <!-- TODO: update product price from here, update all stocks price if they have the same price and return warning if they have multiple prices to update them from stocks tab -->

                    <ul class="list-none p-0 m-0">
                        <info-item label="Code" :value="product.code"/>
                        <info-item label="Name" :value="product.name"/>

                        <info-item
                            label="Description"
                            :value="product.description"
                        />
                        <info-item label="Price" :value="product.price"/>

                        <info-item
                            label="Stock Offset"
                            :value="product.stock_offset"
                        />
                        <info-item
                            label="Category"
                            :value="
                                product.category_id == null
                                    ? 'No Category'
                                    : product.category.name
                            "
                        />
                    </ul>
                </TabPanel>
                <!-- TODO: Add validation on A. quantity and parts & S. quantity and parts & discount and discount limit & expire date -->
                <TabPanel header="Stocks" v-if="can('see product stocks')">
                    <DataTable
                        :value="product.stocks"
                        showGridlines
                        :rowStyle="unavailableStockClass"
                        v-model:editingRows="editingRows"
                        editMode="row"
                        @row-edit-save="updateStock"
                    >
                        <Column
                            field="code"
                            header="Stock Code"
                            class="text-center"
                        />
                        <Column
                            field="original_quantity"
                            header="O. Quantity"
                            class="text-center"
                        >
                            <template #body="{ field, data }">
                                {{ data[field] }}
                            </template>
                            <template #editor="{ field, data }">
                                <InputNumber
                                    v-model="data[field]"
                                    :inputStyle="editInputStyle"
                                />
                            </template>
                        </Column>
                        <Column
                            field="original_parts"
                            header="O. Parts"
                            class="text-center"
                        >
                            <template #body="{ field, data }">
                                {{ data[field] }}
                            </template>
                            <template #editor="{ field, data }">
                                <InputNumber
                                    v-model="data[field]"
                                    :inputStyle="editInputStyle"
                                />
                            </template>
                        </Column>
                        <Column
                            field="available_quantity"
                            header="A. Quantity"
                            class="text-center"
                        >
                            <template #body="{ field, data }">
                                {{ data[field] }}
                            </template>
                            <template #editor="{ field, data }">
                                <InputNumber
                                    v-model="data[field]"
                                    :inputStyle="editInputStyle"
                                />
                            </template>
                        </Column>
                        <Column
                            field="available_parts"
                            header="A. Parts"
                            class="text-center"
                        >
                            <template #body="{ field, data }">
                                {{ data[field] }}
                            </template>
                            <template #editor="{ field, data }">
                                <InputNumber
                                    v-model="data[field]"
                                    :inputStyle="editInputStyle"
                                />
                            </template>
                        </Column>
                        <Column
                            field="sold_quantity"
                            header="S. Quantity"
                            class="text-center"
                        >
                            <template #body="{ field, data }">
                                {{ data[field] }}
                            </template>
                            <template #editor="{ field, data }">
                                <InputNumber
                                    v-model="data[field]"
                                    :inputStyle="editInputStyle"
                                />
                            </template>
                        </Column>
                        <Column
                            field="sold_parts"
                            header="S. Parts"
                            class="text-center"
                        >
                            <template #body="{ field, data }">
                                {{ data[field] }}
                            </template>
                            <template #editor="{ field, data }">
                                <InputNumber
                                    v-model="data[field]"
                                    :inputStyle="editInputStyle"
                                />
                            </template>
                        </Column>
                        <Column
                            field="parts_per_unit"
                            header="Parts/unit"
                            class="text-center"
                        >
                            <template #body="{ field, data }">
                                {{ data[field] }}
                            </template>
                            <template #editor="{ field, data }">
                                <InputNumber
                                    v-model="data[field]"
                                    :inputStyle="editInputStyle"
                                />
                            </template>
                        </Column>
                        <Column
                            field="discount"
                            header="Discount"
                            class="text-center"
                        >
                            <template #body="{ field, data }">
                                {{ data[field] }}
                            </template>
                            <template #editor="{ field, data }">
                                <InputNumber
                                    v-model="data[field]"
                                    :inputStyle="editInputStyle"
                                />
                            </template>
                        </Column>
                        <Column
                            field="discount_limit"
                            header="Discount Limit"
                            class="text-center"
                        >
                            <template #body="{ field, data }">
                                {{ data[field] }}
                            </template>
                            <template #editor="{ field, data }">
                                <InputNumber
                                    v-model="data[field]"
                                    :inputStyle="editInputStyle"
                                />
                            </template>
                        </Column>
                        <Column
                            field="expire_date"
                            header="Exp. Date"
                            class="text-center"
                        >
                            <template #body="{ field, data }">
                                {{ formatExpireDate(data[field]) }}
                            </template>
                            <template #editor="{ field, data }">
                                <InputMask
                                    id="basic"
                                    v-model="data[field]"
                                    placeholder="02/2025"
                                    mask="99/9999"
                                    slotChar="mm/yyyy"
                                    :inputStyle="editInputStyle"
                                />
                                <!-- @update:modelValue="
                                        checkValidCurrentItem($event, 'expDate')
                                    " -->
                            </template>
                        </Column>
                        <Column
                            field="price"
                            header="Price"
                            class="text-center"
                        >
                            <template #body="{ field, data }">
                                {{ data[field] }}
                            </template>
                            <template #editor="{ field, data }">
                                <InputNumber
                                    v-model="data[field]"
                                    :inputStyle="editInputStyle"
                                />
                            </template>
                        </Column>
                        <Column
                            field="supplier"
                            header="Supplier"
                            class="text-center"
                        ></Column>
                        <Column v-if="can('edit stock')" rowEditor/>
                    </DataTable>
                </TabPanel>
            </TabView>

            <!-- TODO: add parts per unit field -->
            <Dialog
                v-model:visible="editDialog"
                header="Product Information"
                :style="{ width: '450px' }"
                class="p-fluid"
            >
                <div class="field">
                    <label for="code">Code</label>
                    <InputText
                        id="code"
                        v-model="productInfo.code"
                        autofocus
                        disabled
                    />
                </div>

                <div class="field">
                    <label for="name">Name</label>
                    <InputText
                        id="name"
                        v-model.trim="productInfo.name"
                        required="true"
                    />
                </div>

                <div class="field">
                    <label for="description">Description</label>
                    <Textarea
                        id="description"
                        v-model="productInfo.description"
                        rows="3"
                        cols="12"
                        autoResize
                    />
                </div>
                <div class="field">
                    <label for="category">Category</label>
                    <Dropdown
                        v-model="productInfo.category_id"
                        :options="categories"
                        editable
                        placeholder="Select a Category"
                        optionLabel="name"
                        optionValue="id"
                    />
                </div>
                <div class="formgrid grid">
                    <div class="field col">
                        <label for="price">Price</label>
                        <InputNumber
                            id="price"
                            v-model="productInfo.price"
                            mode="currency"
                            currency="EGP"
                            locale="en-US"
                            :class="{
                                'p-invalid': submitted && !product.price,
                            }"
                            :required="true"
                        />
                    </div>
                    <div class="field col">
                        <label for="quantity">Stock Offset</label>
                        <InputNumber
                            id="quantity"
                            v-model="productInfo.stock_offset"
                            integeronly
                        />
                    </div>
                </div>
                <template #footer>
                    <Button
                        label="Cancel"
                        icon="pi pi-times"
                        outlined
                        @click="hideProductDialog"
                    />
                    <Button
                        label="Save"
                        icon="pi pi-check"
                        @click="updateProduct"
                    />
                </template>
            </Dialog>

            <Dialog
                header="Confirmation"
                v-model:visible="deleteConfirmation"
                :style="{ width: '450px' }"
                :modal="true"
            >
                <div class="flex align-items-center justify-content-center">
                    <i
                        class="pi pi-exclamation-triangle mr-3"
                        style="font-size: 3rem"
                    />
                    <span
                    >Are you sure you will delete
                        <span class="color-red">{{ product.name }}?</span></span
                    >
                </div>
                <template #footer>
                    <Button
                        label="No"
                        icon="pi pi-times"
                        @click="deleteConfirmation = false"
                        outlined
                    />
                    <Button
                        label="Yes"
                        icon="pi pi-check"
                        @click="deleteProduct"
                        autofocus
                    />
                </template>
            </Dialog>
        </div>
    </app-layout>
</template>

<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import InfoItem from "@/Components/InfoItem.vue";
import {Head, router} from "@inertiajs/vue3";
import {reactive, ref} from "vue";
import {useToast} from "primevue/usetoast";
import {formatExpireDate} from "@/helpers";
import {usePermission} from "../composables/permissions";

const {can} = usePermission()
const toast = useToast();
const props = defineProps({
    product: Object,
    categories: Object,
});

const productInfo = reactive({...props.product});
const editDialog = ref(false);
const deleteConfirmation = ref(false);

const editingRows = ref([]);
const editInputStyle = {width: "10%", "text-align": "center"};

//FIXME: updates can't be visible to the user until the page is reloaded
const updateStock = (event) => {
    const {newData} = event;
    console.log(newData);
    axios.put("/stocks/" + newData.code, {
        original_quantity: newData.original_quantity,
        original_parts: newData.original_parts,
        available_quantity: newData.available_quantity,
        available_parts: newData.available_parts,
        discount: newData.discount,
        discount_limit: newData.discount_limit,
        expire_date: newData.expire_date,
        price: newData.price,
    }).then(() => {
        window.location.reload()
    });
};

const unavailableStockClass = (data) => {
    if (
        data.sold_quantity === data.original_quantity &&
        data.sold_parts === data.available_parts
    ) {
        return {backgroundColor: "var(--surface-hover)"};
    }
};

const hideProductDialog = () => {
    editDialog.value = false;
};

const updateProduct = () => {
    router.put("/products/" + props.product.id, productInfo, {
        onSuccess: (pageObj) => {
            toast.add({
                severity: pageObj.props.flash.severity,
                summary: pageObj.props.flash.message,
                life: 4000,
            });
        },
    });
    hideProductDialog();
};

const deleteProduct = () => {
    router.delete("/products/" + props.product.id);
    deleteConfirmation.value = false;
};
</script>
