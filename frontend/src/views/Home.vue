import axios from 'axios'

axios.get('http://127.0.0.1:8000/api/test')
  .then(res => {
    console.log(res.data)
  })