import { onMounted, ref, reactive, toRefs } from "vue"
import { formatTimeSince } from '@/helpers';
import { router } from "@inertiajs/vue3";

export function useOrders(order) {

    const items = ref([])
    const totalOrderPrice = ref(0)
    const getItems = async () => {
        const response = await axios.get('/order/' + order.reference_code + '/items')
        items.value = response.data
        totalOrderPrice.value = calculateTotalOrderPrice(items.value)
    }


    onMounted(getItems)


    const getProduct = (code) => axios.get('/product/' + code);

    const getNewProduct = async (item) => {
        const product = (await getProduct(item.code)).data;
        item.product_id = product.id;
        item.name = product.name;
        item.unit_price = product.price;
        item.parts_per_unit = product.parts_per_unit;
        //TODO: get stocks of the product for customer
    };
    const submitItem = async (safeToSubmit, item, extraAction = () => { }) => {
        if (!safeToSubmit) return;
        const response = await axios.post('/order/' + order.reference_code + '/items', {
            order_id: order.id,
            product_id: item.product_id,
            quantity: item.quantity,
            parts: item.parts,
            discount: item.discount,
            discount_limit: item.discount_limit,
            unit_price: item.unit_price,
            total_amount: calculateItemTotalPrice(
                item.unit_price,
                item.quantity,
                item.parts,
                item.parts_per_unit,
                item.discount
            ),
            expire_date: item.expDate
        }).catch((error) => console.log(error))

        items.value = response.data.items
        totalOrderPrice.value = calculateTotalOrderPrice(items.value)

        resetForm(item)
        extraAction()
    }


    const updateItem = async (event, current, safeToUpdate, extraAction = () => { }) => {
        console.log(safeToUpdate);
        if (!safeToUpdate) return;

        if (event.field === "product") {
            current.code = event.newData.product.code;
            console.log(current);
            const product = (await getProduct(current.code)).data;
            current.product_id = product.id;
            current.name = product.name;
            current.unit_price = product.price;
            current.parts_per_unit = product.parts_per_unit;
            console.log(current);
        }

        if (event.field === "quantity") current.quantity = event.newData.quantity;
        if (event.field === "parts") current.parts = event.newData.parts;
        if (event.field === "unit_price")
            current.unit_price = event.newData.unit_price;
        if (event.field === "discount") current.discount = event.newData.discount;
        if ('discount_limit' in current && event.field === "discount_limit")
            current.discount_limit = event.newData.discount_limit;
        if (event.field === "expire_date")
            current.expDate = event.newData.expire_date;


        console.log(current);
        router.put(
            "/order/item/" + current.id,
            {
                product_id: current.product_id,
                quantity: current.quantity,
                parts: current.parts,
                unit_price: current.unit_price,
                discount: current.discount,
                discount_limit: current.discount_limit,
                total_amount: calculateItemTotalPrice(
                    current.unit_price,
                    current.quantity,
                    current.parts,
                    current.parts_per_unit,
                    current.discount
                ),
                expire_date: current.expDate,
            },
            {
                onSuccess: (page) => {
                    getItems();
                    extraAction(page)
                },
                onError: (e) => {
                    console.log(e.response);
                },
            }
        );
    };

    const formattedTimeSince = ref(formatTimeSince(order.created_at))

    const updateTodayTimeSince = () => {

        if (new Date(order.created_at).toDateString() === new Date().toDateString()) {
            setInterval(() => {
                formattedTimeSince.value = formatTimeSince(order.created_at);
            }, 1000 * 60)
        }
    }

    const resetForm = (item) => {
        item.product_id = null;
        item.code = "";
        item.name = "";
        item.quantity = 1;
        item.parts = 0;
        item.discount = 0;
        if ('discount_limit' in item) item.discount_limit = 0;
        item.unit_price = 0;
        item.expDate = "";
    };
    return {
        items, getItems,
        totalOrderPrice,
        getProduct,
        getNewProduct,
        submitItem,
        updateItem,
        formattedTimeSince,
        updateTodayTimeSince,
        resetForm
    }
}


//TODO: add validation logic here





export function calculateItemTotalPrice(price, quantity, parts = 0, partsPerUnit = 1, discount = 0) {
    const discountDecimal = discount / 100;
    let totalPrice = 0;
    if (parts == 0)
        totalPrice = (price * quantity) * (1 - discountDecimal);
    else {
        const partsPerUnitChecked = partsPerUnit == null ? 1 : partsPerUnit
        totalPrice = (price * quantity + (price / partsPerUnitChecked) * parts) * (1 - discountDecimal);
    }


    return totalPrice;
}


export function calculateTotalOrderPrice(items) {
    let total = 0;
    items.forEach((item) => {
        total += item.total_amount
    })
    return total
}