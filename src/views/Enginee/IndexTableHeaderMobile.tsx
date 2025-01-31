// ** MUI Imports
import React, { useState, useEffect, Fragment, Ref, useRef } from 'react'
import Box from '@mui/material/Box'
import Button from '@mui/material/Button'
import TextField from '@mui/material/TextField'
import Mousetrap from 'mousetrap'

// ** Icon Imports
import Grid from '@mui/material/Grid'
import MenuItem from '@mui/material/MenuItem'
import InputLabel from '@mui/material/InputLabel'
import FormControl from '@mui/material/FormControl'
import CardContent from '@mui/material/CardContent'
import Select, { SelectChangeEvent } from '@mui/material/Select'

import Dialog from '@mui/material/Dialog'
import DialogTitle from '@mui/material/DialogTitle'
import DialogContent from '@mui/material/DialogContent'
import DialogActions from '@mui/material/DialogActions'
import DialogContentText from '@mui/material/DialogContentText'

import { GridRowId } from '@mui/x-data-grid'
import toast from 'react-hot-toast'

import { useForm, Controller } from 'react-hook-form'

interface TableHeaderProps {
  filter: any[]
  handleFilterChange: (field: any, value: string) => void
  handleFilter: (val: string) => void
  toggleAddTableDrawer: (val: string) => void
  toggleImportTableDrawer: () => void
  toggleExportTableDrawer: () => void
  value: string
  searchFieldText: string
  searchFieldArray: { value: string; }[]
  selectedRows: GridRowId[]
  multireview: {multireview:{}[]}
  multiReviewHandleFilter: (action: string, multiReviewInputValue: string, selectedRows: GridRowId[], CSRF_TOKEN: string) => void
  button_search: string
  button_add: string
  button_import: string
  button_export: string
  isAddButton: boolean
  isImportButton: boolean
  isExportButton: boolean
  CSRF_TOKEN: string
  MobileEndShowSearch: string
  MobileEndShowGroupFilter: string
}

const IndexTableHeaderMobile = (props: TableHeaderProps) => {

  // ** Props
  const { filter, handleFilterChange, handleFilter, toggleAddTableDrawer, toggleImportTableDrawer, toggleExportTableDrawer, searchFieldText, searchFieldArray, selectedRows, multireview, multiReviewHandleFilter, button_search, CSRF_TOKEN, MobileEndShowSearch, MobileEndShowGroupFilter } = props
  const defaultValuesInitial = { "searchFieldName": searchFieldArray && searchFieldArray[0] && searchFieldArray[0].value ? searchFieldArray[0].value : undefined, "searchFieldValue": "", "multiReviewInputName": "" }

  //console.log("IndexTableHeaderMobile props", props)

  const defaultValues = JSON.parse(JSON.stringify(defaultValuesInitial))
  const [filterSelectValue, setFilterSelectValue] = useState<any[]>([])

  useEffect(() => {

    //Mousetrap.bind(['alt+f', 'command+f'], handleSubmit(onSubmit));
    Mousetrap.bind(['alt+a', 'command+a'], ()=>toggleAddTableDrawer(''));
    Mousetrap.bind(['alt+i', 'command+i'], toggleImportTableDrawer);
    Mousetrap.bind(['alt+e', 'command+e'], toggleExportTableDrawer);

    return () => {
      Mousetrap.unbind(['alt+f', 'command+f']);
      Mousetrap.unbind(['alt+a', 'command+a']);
      Mousetrap.unbind(['alt+i', 'command+i']);
      Mousetrap.unbind(['alt+e', 'command+e']);
    }
  });

  //console.log("defaultValuesInitial", defaultValuesInitial)
  //console.log("defaultValues", defaultValues)
  //console.log("JSON.parse(JSON.stringify(filter))***********", JSON.parse(JSON.stringify(filter)))
  //console.log("filter", filter)
  //console.log("filter*******************************", filter)
  //console.log("searchFieldArray*******************************", searchFieldArray)

  const {
    setValue,
    control,
    handleSubmit,
    formState: { errors }
  } = useForm({
    defaultValues: defaultValues,
    mode: 'onChange'
  })

  const onSubmit = (data: any) => {
    setValue("searchFieldName", data.searchFieldName)
    handleFilter(data)
  }

  const [multiReviewInputValue, setMultiReviewInputValue] = useState('')
  const handleMultiReviewAction = (action: string, selectedRows: GridRowId[]) => {
    multiReviewHandleFilter(action, multiReviewInputValue, selectedRows, CSRF_TOKEN)
    setMultiReviewInputValue('')
  }
  const [multiReviewOpenDialog, setMultiReviewOpenDialog] = useState<{[key:string]:any}>({})
  const handleMultiOpenDialog = (action: string) => {
    const multiReviewOpenDialogNew:{[key:string]:any} = {}
    multireview.multireview.map((Item: any) => {
      multiReviewOpenDialogNew[Item.action] = false
    })
    multiReviewOpenDialogNew[action] = true
    setMultiReviewOpenDialog(multiReviewOpenDialogNew)
  }
  const handleMultiCloseDialog = () => {
    const multiReviewOpenDialogNew:{[key:string]:any} = {}
    multireview.multireview.map((Item: any) => {
      multiReviewOpenDialogNew[Item.action] = false
    })
    setMultiReviewOpenDialog(multiReviewOpenDialogNew)
  }
  const handleMultiCloseDialogAndSubmit = (action: string, selectedRows: GridRowId[], Item: any) => {
    if (Item.inputmust && Item.memoname != "" && multiReviewInputValue == '') {
      toast.error(Item.inputmusttip)
    }
    else {
      handleMultiCloseDialog()
      handleMultiReviewAction(action, selectedRows)
    }
  }

  const myRef:Ref<any> = useRef(null)

  //setValue("searchFieldName", searchFieldArray[0].value)
  //console.log("searchFieldNamesearchFieldNamesearchFieldName", searchFieldArray[0].value)

  return (
    <>
      <form onSubmit={handleSubmit(onSubmit)} id="searchOneField">
        {filter.length > 0 && MobileEndShowGroupFilter=='Yes' ?
          <CardContent sx={{ pl: 3, pb: 0, pt: 4 }}>
            <Grid container spacing={6}>
              {filter.length > 0 && filter.map((Filter: any, Filter_index: number) => {

                //const [valueFunction, setStatusFunction] = FilterStateMap['Filter_'+Filter_index];

                return (
                  <Grid item sm={3} xs={6} key={"Filter_" + Filter_index}>
                    <FormControl fullWidth size="small">
                      <InputLabel id={Filter.name}>{Filter.text}</InputLabel>
                      <Select
                        fullWidth
                        value={filterSelectValue[Filter_index] || [Filter.selected]}
                        id={Filter.text}
                        label={Filter.name}
                        labelId={Filter.text}
                        onChange={(e: SelectChangeEvent) => {
                          handleFilterChange(Filter.name, e.target.value)
                          filterSelectValue[Filter_index] = e.target.value
                          setFilterSelectValue(filterSelectValue);
                          if(filterSelectValue && filterSelectValue.length == 1 && filterSelectValue[0].length == 0) {
                            filterSelectValue[Filter_index] = [Filter.selected]
                            setFilterSelectValue(filterSelectValue);
                          }
                        }}
                        inputProps={{ placeholder: Filter.text }}
                      >
                        {filterSelectValue[Filter_index]!=undefined && Filter && Filter.list.map((item: any, item_index: number) => {
                          return (
                            <MenuItem value={item.value} key={item.name + "_" + item_index}>{item.name}({item.num})</MenuItem>
                          )

                          //<Checkbox size="small" style={{padding:'0px 5px 0px 0px'}} checked={ (filterSelectValue[Filter_index] && filterSelectValue[Filter_index].includes(item.value) ) } />
                        })}
                        {filterSelectValue[Filter_index]==undefined && Filter && Filter.list.map((item: any, item_index: number) => {
                          return (
                            <MenuItem value={item.value} key={item.name + "_" + item_index}>{item.name}({item.num})</MenuItem>
                          )

                          //<Checkbox size="small" style={{padding:'0px 5px 0px 0px'}} checked={Filter.selected == item.value} />
                        })}
                      </Select>
                    </FormControl>
                  </Grid>
                )

              })}

            </Grid>
          </CardContent>
          : ''
        }
        {(!selectedRows || selectedRows.length == 0) && MobileEndShowSearch=='Yes' ?
          <Box sx={{ pt: 4, px: 3, pb: 2, display: 'flex', flexWrap: 'wrap', alignItems: 'center', justifyContent: 'space-between' }}>
            <Grid container spacing={2}>
              {searchFieldArray ?
                <Grid item xs={12}>
                  <FormControl fullWidth size="small">
                    <InputLabel id={searchFieldText}>{searchFieldText}</InputLabel>
                    <Controller
                      name='searchFieldName'
                      control={control}
                      render={({ field: { value, onChange } }) => (
                        <Select
                          value={value}
                          label={searchFieldText}
                          onChange={(e: SelectChangeEvent) => {
                            onChange(e);
                            console.log("E", e.target.value)
                          }}
                          error={Boolean(errors['searchFieldName'])}
                          labelId='validation-basic-select'
                          aria-describedby='validation-basic-select'
                        >
                          {searchFieldArray && searchFieldArray.map((ItemArray: any, ItemArray_index: number) => {
                            return <MenuItem value={ItemArray.value} key={"SelectedRows_" + ItemArray_index}>{ItemArray.label}</MenuItem>
                          })}
                        </Select>
                      )}
                    />
                  </FormControl>
                </Grid>
                : ''}
              {searchFieldArray ?
                <Grid item xs={12}>
                  <FormControl fullWidth size="small" sx={{mt: 1}}>
                    <Controller
                      name="searchFieldValue"
                      control={control}
                      render={({ field: { value, onChange } }) => (
                        <TextField
                          size='small'
                          value={value}
                          label={searchFieldText}
                          onChange={onChange}
                          placeholder={searchFieldText}
                          error={Boolean(errors['searchFieldValue'])}
                        />
                      )}
                    />
                  </FormControl>
                </Grid>
                : ''}
              {searchFieldArray ?
                <Grid item sm={12} sx={{width: 'calc(100%)'}}>
                  <FormControl fullWidth>
                    <Button variant='contained' type='submit'>{button_search}</Button>
                  </FormControl>
                </Grid>
                : ''}
            </Grid>
          </Box>
          : ''
        }
      </form>
      {selectedRows && selectedRows.length > 0 ?
        <Box sx={{ pl: 3, pb: 2, display: 'flex', flexWrap: 'wrap', alignItems: 'center', justifyContent: 'space-between' }}>
          <Grid container spacing={2}>
            {multireview && multireview.multireview && multireview.multireview.map((Item: any, index: number) => {

              return (
                <Grid item key={"Grid_" + index}>
                  <Fragment>
                    <Button sx={{ mb: 2 }} variant='contained' type="button" onClick={() => handleMultiOpenDialog(Item.action)}>{Item.text}</Button>
                    <Dialog
                      open={multiReviewOpenDialog[Item.action] == undefined ? false : multiReviewOpenDialog[Item.action]}
                      onClose={() => handleMultiCloseDialog()}
                      aria-labelledby='form-dialog-title'
                    >
                      <DialogTitle id='form-dialog-title'>{Item.title}</DialogTitle>
                      <DialogContent>
                        <DialogContentText sx={{ mb: 3 }}>
                          {Item.content}
                        </DialogContentText>
                        {Item.memoname != "" ? <TextField required={Item.inputmust} inputRef={myRef} id={Item.memoname} value={multiReviewInputValue} onChange={(e) => { setMultiReviewInputValue(e.target.value) }} autoFocus fullWidth type='text' label={Item.memoname} /> : ''}
                      </DialogContent>
                      <DialogActions className='dialog-actions-dense'>
                        <Button onClick={() => handleMultiCloseDialog()}>{Item.cancel}</Button>
                        {Item.memoname != "" ?
                          <Button onClick={() => { myRef.current.reportValidity(); handleMultiCloseDialogAndSubmit(Item.action, selectedRows, Item) }} variant='contained'>{Item.submit}</Button>
                          :
                          <Button onClick={() => { handleMultiCloseDialogAndSubmit(Item.action, selectedRows, Item) }} variant='contained'>{Item.submit}</Button>
                        }
                      </DialogActions>
                    </Dialog>
                  </Fragment>
                </Grid>
              )
            })}
          </Grid>
        </Box>
        : ''
      }
    </>
  )
}

export default React.memo(IndexTableHeaderMobile);

