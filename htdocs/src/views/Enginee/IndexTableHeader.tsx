// ** MUI Imports
import React, { useState, useEffect, Fragment, Ref, useRef } from 'react'
import Box from '@mui/material/Box'
import Button from '@mui/material/Button'
import TextField from '@mui/material/TextField'
import Mousetrap from 'mousetrap'

// ** Icon Imports
import Grid from '@mui/material/Grid'
import Divider from '@mui/material/Divider'
import MenuItem from '@mui/material/MenuItem'
import InputLabel from '@mui/material/InputLabel'
import FormControl from '@mui/material/FormControl'
import CardContent from '@mui/material/CardContent'
import Tooltip from "@mui/material/Tooltip"
import Select, { SelectChangeEvent } from '@mui/material/Select'

import Dialog from '@mui/material/Dialog'
import DialogTitle from '@mui/material/DialogTitle'
import DialogContent from '@mui/material/DialogContent'
import DialogActions from '@mui/material/DialogActions'
import DialogContentText from '@mui/material/DialogContentText'

import { GridRowId } from '@mui/x-data-grid-pro'
import toast from 'react-hot-toast'

import {isMobile} from 'src/configs/functions'

import { useForm, Controller } from 'react-hook-form'

interface TableHeaderProps {
  filter: any[]
  handleFilterChange: (field: any, value: string) => void
  handleFilter: (val: string) => void
  toggleAddTableDrawer: () => void
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

const IndexTableHeader = (props: TableHeaderProps) => {
  
  // ** Props
  const { filter, handleFilterChange, handleFilter, toggleAddTableDrawer, toggleImportTableDrawer, toggleExportTableDrawer, searchFieldText, searchFieldArray, selectedRows, multireview, multiReviewHandleFilter, button_search, button_add, button_import, button_export, isAddButton, isImportButton, isExportButton, CSRF_TOKEN, MobileEndShowSearch, MobileEndShowGroupFilter } = props
  console.log("IndexTableHeader props", props)
  const defaultValuesInitial = { "searchFieldName": searchFieldArray && searchFieldArray[0] && searchFieldArray[0].value ? searchFieldArray[0].value : undefined, "searchFieldValue": "", "multiReviewInputName": "" }
  
  const defaultValues = JSON.parse(JSON.stringify(defaultValuesInitial))
  const [filterSelectValue, setFilterSelectValue] = useState<any[]>([])

  const isMobileData = isMobile()

  useEffect(() => {
    
    //Mousetrap.bind(['alt+f', 'command+f'], handleSubmit(onSubmit));
    Mousetrap.bind(['alt+a', 'command+a'], toggleAddTableDrawer);
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
        {filter.length > 0 && ( (isMobileData==false) || (isMobileData==true && MobileEndShowGroupFilter=='Yes')) ?
          <CardContent sx={{ pl: 3, pb: 1, pt: 1 }}>
            <Grid container spacing={6}>
              {filter.length > 0 && filter.map((Filter: any, Filter_index: number) => {
                
                //const [valueFunction, setStatusFunction] = FilterStateMap['Filter_'+Filter_index];
                
                return (
                  <Grid item sm={3} xs={6} key={"Filter_" + Filter_index}>
                    <FormControl fullWidth size="small">
                      <InputLabel id={Filter.name}>{Filter.text}</InputLabel>
                      <Select
                      
                        //multiple
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
              {isAddButton && (isMobileData==true && MobileEndShowSearch=='No') ?
              <Grid item sm={3} xs={6}>
                <Tooltip title="Alt+a">
                  <Button sx={{ ml: 0, mb: 0 }} onClick={toggleAddTableDrawer} variant='contained'>{button_add}</Button>
                </Tooltip>
              </Grid>
              : 
              null}
            </Grid>
          </CardContent>
          : ''
        }
        {filter.length > 0 && ( (isMobileData==false) || (isMobileData==true && MobileEndShowGroupFilter=='Yes' && MobileEndShowSearch=='Yes')) ? <Divider /> : ''}
        {(!selectedRows || selectedRows.length == 0) && ( (isMobileData==false) || (isMobileData==true && MobileEndShowSearch=='Yes')) ?
          <Box sx={{ pl: 5, pb: 2, display: 'flex', flexWrap: 'wrap', alignItems: 'center', justifyContent: 'space-between' }}>
            <Grid container spacing={2}>
              {searchFieldArray ?
                <Grid item sm={3} xs={6}>
                  <FormControl fullWidth={!isMobileData} size="small">
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
                <Grid item sm={2} xs={5}>
                  <FormControl fullWidth={!isMobileData} size="small" sx={{}}>
                    <Controller
                      name="searchFieldValue"
                      control={control}
                      render={({ field: { value, onChange } }) => (
                        <TextField
                          size='small'
                          value={value}
                          sx={{ mb: 2 }}
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
                <Grid item sm={2} xs={6}>
                  <FormControl fullWidth size="small" sx={{}}>
                    <Tooltip title="Alt+f">
                      <Button sx={{ ml: 3, mb: 2 }} variant='contained' type='submit'>{button_search}</Button>
                    </Tooltip>
                  </FormControl>
                </Grid>
                : ''}
              {isAddButton || isImportButton || isExportButton ?
                <Grid item sm={4} xs={6}>
                  {isAddButton ? 
                  <Tooltip title="Alt+a">
                    <Button sx={{ ml: 3, mb: 2 }} onClick={toggleAddTableDrawer} variant='contained'>{button_add}</Button>
                  </Tooltip>
                  : ''}
                  {isMobileData == false && isImportButton ? 
                  <Tooltip title="Alt+i">
                    <Button sx={{ ml: 3, mb: 2 }} onClick={toggleImportTableDrawer} variant='contained'>{button_import}</Button>
                  </Tooltip>
                  : ''}
                  {isMobileData == false && isExportButton ? 
                  <Tooltip title="Alt+e">
                    <Button sx={{ ml: 3, mb: 2 }} onClick={toggleExportTableDrawer} variant='contained'>{button_export}</Button>
                  </Tooltip>
                  : ''}
                </Grid>
                : ''}
            </Grid>
          </Box>
          : ''
        }
      </form>
      {selectedRows && selectedRows.length > 0 ?
        <Box sx={{ pl: 5, pb: 2, display: 'flex', flexWrap: 'wrap', alignItems: 'center', justifyContent: 'space-between' }}>
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

export default React.memo(IndexTableHeader);

