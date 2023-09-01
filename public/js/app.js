Vue.component('category-list', {
    props: ['categories'],
    template: `
    <div>
        <h2>Список категорий</h2>
        <ul>
            <li v-for="category in categories" :key="category.id">
                {{ category.name }}
                <button @click="editCategory(category)">Редактировать</button>
                <button @click="deleteCategory(category.id)">Удалить</button>
            </li>
        </ul>
    </div>
    `,
    methods: {
        editCategory(category) {
            this.$emit('edit-category', category);
        },
        // Другие методы
    },

});
