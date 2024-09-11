// ** Redux Imports
import { createSlice, createAsyncThunk } from '@reduxjs/toolkit'

// ** Axios Imports
import axios from 'axios'

// ** Config
import authConfig from 'src/configs/auth'

// ** Fetch Users
export const fetchData = createAsyncThunk('appUsers/fetchData', async (params: any) => {
  const newAllFilters = JSON.parse(JSON.stringify(params['allSubmitFields']))
  newAllFilters['externalId'] = params['externalId']
  newAllFilters['page'] = params['page']
  newAllFilters['pageSize'] = params['pageSize']
  newAllFilters['searchFieldName'] = params['searchFieldName']
  newAllFilters['searchFieldValue'] = params['searchFieldValue']
  newAllFilters['sortMethod'] = params['sortMethod']
  newAllFilters['sortColumn'] = params['sortColumn']
  if (params['filterMultiColumns'] != undefined) {
    newAllFilters['filterMultiColumns'] = JSON.parse(JSON.stringify(params['filterMultiColumns']))
  }

  const backEndApi = "test_store.php";
  const response = await axios.get(authConfig.backEndApiHost + backEndApi, {
    params: newAllFilters
  })

  return response.data

})

export const appUsersSlice = createSlice({
  name: 'appUsers',
  initialState: {
    data: [],
    total: 1,
    params: {},
    filter: [],
    columns: [{field:''}],
    init_action: {action:'',id:-1},
    init_default: {searchtitle:'',pinnedColumns:{},rowdelete:[''],dataGridLanguageCode:'',ForbiddenViewRow:[''],ForbiddenEditRow:[''],ForbiddenDeleteRow:[''],ForbiddenSelectRow:'',returnButton:'',searchFieldText:'',rowHeight:'',checkboxSelection:'',searchFieldArray:[{value:''}],multireview:{multireview:[{}]},},
    add_default: {defaultValues:{},allFields:[],allFieldsMode:[],titletext:'',titlememo:'',},
    edit_default: {defaultValues:{}},
    edit_default_configsetting: [],
    edit_default_1: [],
    edit_default_2: [],
    edit_default_3: [],
    edit_default_4: [],
    edit_default_5: [],
    edit_default_6: [],
    view_default: {defaultValues:{},allFields:[[]],allFieldsMode:[],titletext:'',titlememo:'',},
    view_default_1: [],
    view_default_2: [],
    view_default_3: [],
    view_default_4: [],
    view_default_5: [],
    export_default: [],
    import_default: [],
    rowHeight: 60,
    pageNumber: 10,
    pageNumberArray: [10, 20, 30, 40, 50, 100, 200, 500]
  },
  reducers: {},
  extraReducers: builder => {
    builder.addCase(fetchData.fulfilled, (state:{[key:string]:any}, action) => {
      state.data = action.payload.init_default.data
      state.total = action.payload.init_default.total
      state.params = action.payload.init_default.params
      state.filter = action.payload.init_default.filter
      state.columns = action.payload.init_default.columns
      state.init_action = action.payload.init_action
      state.init_default = action.payload.init_default
      state.add_default = action.payload.add_default
      state.edit_default = action.payload.edit_default
      state.view_default = action.payload.view_default
      state.export_default = action.payload.export_default
      state.import_default = action.payload.import_default
      state.edit_default_configsetting = action.payload.edit_default_configsetting
      state.edit_default_1 = action.payload.edit_default_1
      state.edit_default_2 = action.payload.edit_default_2
      state.edit_default_3 = action.payload.edit_default_3
      state.edit_default_4 = action.payload.edit_default_4
      state.edit_default_5 = action.payload.edit_default_5
      state.edit_default_6 = action.payload.edit_default_6
      state.view_default_1 = action.payload.view_default_1
      state.view_default_2 = action.payload.view_default_2
      state.view_default_3 = action.payload.view_default_3
      state.view_default_4 = action.payload.view_default_4
      state.view_default_5 = action.payload.view_default_5
    })
  }
})

export default appUsersSlice.reducer
