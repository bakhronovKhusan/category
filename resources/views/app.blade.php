<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laravel VUE CRUD Application - LaravelTuts</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14"></script>
    <!-- Include Bootstrap CSS via CDN -->
    <link
        rel="stylesheet"
        href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
    >

    <!-- Include Bootstrap JavaScript and Popper.js via CDN -->
    <script
        src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj"
        crossorigin="anonymous"
    ></script>

    <script
        src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"
    ></script>
</head>
<body>
<div id="app">
    <div class="container" id='Show'><br>

        <div class="col-12">
            <div class="row">
                <div class="col-6">
                    <label for="" style="display: block"> Name
                        <input type="text" v-model="newName" class="form-control">
                    </label>
                </div>
                <div class="col-6">
                    <label for="" style="display: block">Description
                        <textarea v-model="newDescription" class="form-control"></textarea>
                    </label>
                </div>
                <button style="width: 50%;margin: 10px auto" @click="addCategory" class="btn btn-outline-info">add</button>
            </div>
        </div>
        <hr>
        <h3 style="text-align: center;">Category lists</h3>
        <div class="col-12">
            <label for="">
                Search:
                <input v-model="searchText" type="text" @change="search">
            </label>
            <label for="">
                Page-Size:
                <input v-model="pageSize" value="2" type="number" @keyup="setPageSize" min="1" max="10">
            </label>
        </div>
        <hr>
        <div class="col-12">
            <table class="table">
                <thead>
                <tr>
                    <th style="color: red">Click For Filter:</th>
                    <th >ID</th>
                    <th data-type='name' data-value='' @click="orderBy" style="border:1px solid #0c5460;cursor: pointer">Name</th>
                    <th>Description</th>
                    <th data-type='created_at' data-value='' @click="orderBy" style="border:1px solid #0c5460;cursor: pointer">Created Date</th>
                    <th data-type='status' data-value=''  @click="orderBy" style="border:1px solid #0c5460;cursor: pointer">Status</th>
                    <th>Delete</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="category in categories" :key="category.id">
                    <td>
                        <div style="display: none;" class="saveText">
                            <button class="btn btn-outline-info" :data-id="category.id" @click="updateRow">Save</button>
                            <button class="btn btn-outline-dark" @click="cancelRow">Cancel</button>
                        </div>
                    </td>
                    <td>@{{ category.id }}</td>
                    <td data-type='name' @dblclick="editRow" >@{{ category.name }}</td>
                    <td data-type='description' @dblclick="editRow" >@{{ category.description }}</td>
                    <td>@{{ formatDateTime(category.created_at) }}</td>
                    <td>
                        <button :class="{ 'btn btn-outline-info': category.status, 'btn btn-outline-dark': !category.status }" @click="statusChange(category.id, category.status)">
                            @{{ category.status ? 'Active' : 'Disabled' }}
                        </button>
                    </td>
                    <td><button @click="deleteCoty(category.id)" class="btn btn-outline-dark">Delete</button></td>
                </tr>
                <tr v-if="categories.length===0">
                    <td style="text-align: center" colspan="7"> Not found! </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="container mt-5">
            <!-- Your content goes here -->
            <!-- Pagination -->
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <li v-for="{url, active, label} in paginate.links" @click ="page(url)" :class="{ 'page-item disabled': !url || active }"><span style="cursor: pointer" class="page-link" v-html="label"></span></li>

                </ul>
            </nav>
        </div>

    </div>

</div>
<script>

    new Vue({
        el: '#app',
        created() {
            this.fetchData()
        },
        data: {
            localURL: '/api/categories/',
            categories: [], // To store the response data
            paginate: [],
            searchText: '',
            flash_url: null,
            pageSize: 2,
            orderByValue: 'created_at',
            orderByType: '',
            newName: '',
            newDescription: '',
            oldRowValue:'',
            oldRowType:'',
            oldRowId:''
        },
        methods: {
            fetchData(url = null) {
                this.flash_url = url ?? this.localURL
                axios.get(this.flash_url)
                    .then((response) => {
                        this.categories = response.data[0].data.data;
                        this.paginate = response.data[0].data;
                    })
                    .catch((error) => {
                        console.error(error);
                    });
            },
            page(url){
                this.fetchData(url)
            },
            deleteCoty(id){
                const userConfirmed = confirm("Are you sure delete?");
                if (userConfirmed) {
                    axios.delete(this.localURL + id)
                        .then((response) => {
                            alert(response.data[0].status + ' / ' + response.data[0].data)
                            if (response.data[0].status === 'success') {
                                location.reload()
                            }
                        })
                        .catch((error) => {
                            console.error(error);
                        });
                }
            },
            search() {
                this.fetchData(this.localURL +'?search='+this.searchText+'&pageSize=' + this.pageSize)
            },
            setPageSize(){
                if(this.searchText.replace(/\s+/g,'')==='') {
                    this.fetchData(this.localURL +'?pageSize=' + this.pageSize)
                } else {
                    this.fetchData(this.localURL + '?search='+this.searchText+'&pageSize=' + this.pageSize)
                }
            },
            orderBy(event){
                const dataType = event.target.getAttribute('data-type');
                const dataValue = event.target.getAttribute('data-value');
                if(this.searchText.replace(/\s+/g,'')==='') {
                    this.fetchData(this.localURL + '?pageSize=' + this.pageSize+'&sort='+dataValue+dataType)
                } else {
                    this.fetchData(this.localURL +'?search='+this.searchText+'&pageSize=' + this.pageSize +'&sort='+dataValue+dataType)
                }
                event.target.setAttribute('data-value', ( dataValue === '' ? '-' : '' ) )
            },
            formatDateTime(dateTime) {
                const originalDate = new Date(dateTime);
                const options = {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: false, // Use 24-hour format
                };
                return originalDate.toLocaleDateString('en-US', options);
            },
            addCategory(){
                if(this.newName.replace(/\s+/g,'')===''){
                    alert("Name was empty!"); return;
                }
                const postData = {
                    name:        this.newName.replace(/\s+/gi,' '),
                    status:      true,
                    description: this.newDescription
                }
                const url = '/api/categories';
                axios.post(url, postData)
                    .then(response => {
                        alert(response.data[0].status + ' / ' + response.data[0].data)
                        if (response.data[0].status === 'success') {
                            location.reload()
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            },
            editRow(event){
                this.oldRowValue = event.target.textContent.replace(/\s+/g,' ');
                this.oldRowType =  event.target.getAttribute('data-type')
                    event.target.setAttribute('contenteditable', true);
                    event.target.parentNode.querySelector('td>div').setAttribute('style','display:block')
            },
            updateRow(event){
                let div = event.target.parentNode.parentNode.parentNode.querySelector("td[data-type='"+this.oldRowType+"']")
                let newText = div.textContent.replace(/\s+/g,' ');


                if(this.oldRowValue===newText){
                    div.removeAttribute('contenteditable')
                    event.target.parentNode.parentNode.querySelector('div').setAttribute('style','display:none')
                }else{
                    const userConfirmed = confirm("Are you sure update?");
                    if (userConfirmed) {
                        const postData = {
                            [this.oldRowType]: newText
                        }
                        const url = '/api/categories/'+event.target.getAttribute('data-id');
                        axios.put(url, postData)
                            .then(response => {
                                alert(response.data[0].status + ' / ' + response.data[0].data)
                                if (response.data[0].status === 'success') {
                                    location.reload()
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                            });
                    }else {
                        div.removeAttribute('contenteditable')
                        event.target.parentNode.parentNode.parentNode.querySelector("td[data-type='"+this.oldRowType+"']").textContent = this.oldRowValue
                        event.target.parentNode.parentNode.querySelector('div').setAttribute('style','display:none')
                    }
                }
            },
            cancelRow(event){
                let div = event.target.parentNode.parentNode.parentNode.querySelector("td[data-type='"+this.oldRowType+"']")
                    div.removeAttribute('contenteditable')
                    event.target.parentNode.parentNode.parentNode.querySelector("td[data-type='"+this.oldRowType+"']").textContent = this.oldRowValue
                    event.target.parentNode.parentNode.querySelector('div').setAttribute('style','display:none')
            },
            statusChange(id, status){
                const userConfirmed = confirm("Are you sure change of status ?");
                if (userConfirmed) {
                    const postData = {
                        status: (status ? 0 : 1)
                    }
                    const url = '/api/categories/'+id;
                    axios.put(url, postData)
                        .then(response => {
                            alert(response.data[0].status + ' / ' + response.data[0].data)
                            if (response.data[0].status === 'success') {
                                location.reload()
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                }
            }
        },
    });
</script>
</body>
</html>
