import React, {Fragment, useState, ChangeEvent, useEffect} from 'react';
import { TreeView, TreeItem } from '@mui/lab';

import TextField from '@mui/material/TextField'

// ** Config
import authConfig from 'src/configs/auth'
import axios from 'axios'

interface Node {
  id: string;
  name: string;
  children?: Node[];
}

interface IndexJumpDialogWindowType {
    handleDialogWindowCloseWithParam: (field: string, value: string, fieldCode: string, valueCode: string) => void
    NewFieldName: string
    NewFieldCode: string
    FieldArray: any
  }

const IndexJumpDialogWindow = ({handleDialogWindowCloseWithParam, NewFieldName, NewFieldCode, FieldArray}: IndexJumpDialogWindowType) => {
  const [textFieldValue, setTextFieldValue] = useState('');
  const [data, setData] = useState([]);

  const handleButtonClick = (item: Node) =>                     {
    if(String(item.id).length==16) {
        handleDialogWindowCloseWithParam(NewFieldName, item.name, NewFieldCode, item.id)
    }
    console.log('Button Clicked for Item ID:', item, item.id.length);
  };

  const renderTree = (nodes: Node) => (
    <TreeItem key={String(nodes.id)} nodeId={String(nodes.id)} label={nodes.name} onClick={()=>{handleButtonClick(nodes)}}>
      {Array.isArray(nodes.children)
        ? nodes.children.map((node) => renderTree(node))
        : null}
    </TreeItem>
  );
  
  //console.log("view_default--------------------------------", id, action)
  const storedToken = window.localStorage.getItem(authConfig.storageTokenKeyName)!

  useEffect(() => {
    const backEndApi = "tree_fixedasset_classification.php"
    axios
    .get(authConfig.backEndApiHost + backEndApi, { headers: { Authorization: storedToken }, params: { textFieldValue }})
    .then(res => {
        setData(res.data)
        console.log("res.data", res.data)
    })
    .catch(() => {
        console.log("axios.get editUrl return")
    })
  }, [textFieldValue])


  const handleTextFieldChange = (event: ChangeEvent<HTMLInputElement>) => {
    const newValue = event.target.value;
    setTextFieldValue(newValue);
  };

  return (
    <Fragment>
        <TextField fullWidth sx={{ mb: 4 }} label={FieldArray.jumpWindowSearchFiledText} size='small' placeholder={FieldArray.jumpWindowSearchFiledPlaceholder} onChange={handleTextFieldChange}/>
        <TreeView    
        sx={{ minHeight: 240 }}
        defaultExpandIcon={<span>+</span>}
        defaultCollapseIcon={<span>-</span>}
        >
        {data && data.length>0 ?
            data.map((node: Node) => renderTree(node))
            : ''
        }
        </TreeView>
    </Fragment>
  );

};

export default IndexJumpDialogWindow;
