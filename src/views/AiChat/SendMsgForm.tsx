// ** React Imports
import { useState, SyntheticEvent } from 'react'

// ** MUI Imports
import Button from '@mui/material/Button'
import { styled } from '@mui/material/styles'
import Box from '@mui/material/Box'
import CircularProgress from '@mui/material/CircularProgress'
import TextareaAutosize from '@mui/material/TextareaAutosize'
import { useTheme } from '@mui/material/styles'

const Form = styled('form')(({ theme }) => ({
  padding: theme.spacing(0, 5, 5)
}))

const SendMsgForm = (props: any) => {
  // ** Props
  const { store, sendMsg, sendButtonDisable, sendButtonText, sendInputText, rowInMsg, handleSetRowInMsg, maxRows } = props

  // ** State
  const [msg, setMsg] = useState<string>('')

  const theme = useTheme();

  const handleSendMsg = (e: SyntheticEvent) => {
    e.preventDefault()
    if (store && store.selectedChat && msg.trim().length) {
      sendMsg({ ...store.selectedChat, message: msg, template: '' })
    }
    setMsg('')
    handleSetRowInMsg(1)
  }


  const handleKeyDown = (e: any) => {
    if (e.key === 'Enter') {
      if (!e.shiftKey) {
        e.preventDefault(); // 阻止默认的换行行为
        if (msg.trim().length) {
          sendMsg({ ...store.selectedChat, message: msg, template: '' }); // 发送消息
          setMsg(''); // 清空文本框
          handleSetRowInMsg(1)
        }
      }
      else {
        e.preventDefault(); // 阻止默认的换行行为
        // 获取当前光标位置并在此位置插入换行符
        const cursorPosition = e.target.selectionStart;
        const textBeforeCursor = msg.substring(0, cursorPosition);
        const textAfterCursor = msg.substring(cursorPosition);
        setMsg(`${textBeforeCursor}\n${textAfterCursor}`);
        handleSetRowInMsg(rowInMsg + 1)
      }
    }
  };

  const handleChange = (e: any) => {
    setMsg(e.target.value);
    const textarea = e.target;
    const newLineCount = textarea.value.split('\n').length;
    handleSetRowInMsg(newLineCount);
  };

  return (
    <Box sx={{ width: '100%', }} >
      <Form onSubmit={handleSendMsg} sx={{mb: 0, pb: 1, mx: 0, px: 0, borderRadius: theme.shape.borderRadius, backgroundColor: theme.palette.background.paper,}}>
        <Box sx={{ display: 'flex', position: 'relative', flexGrow: 1 }}>
          <TextareaAutosize
            minRows={rowInMsg}
            maxRows={maxRows}
            value={msg}
            placeholder={sendInputText}
            onChange={handleChange}
            onKeyDown={handleKeyDown}
            disabled={sendButtonDisable}
            style={{
                width: 'calc(100% - 100px)', // 减去按钮宽度
                marginRight: '2px', // 为按钮留出空间
                resize: 'none',
                border: 'none', // 移除边框
                padding: '0.5rem 0.1rem 0.5rem 0.5rem',
                fontFamily: 'inherit', // 使用默认字体
                fontWeight: '1000', // 使用默认字体粗细
                fontSize: '1rem', // 使用默认字体大小
                outline: 'none', // 默认状态下无边框
                boxShadow: 'none', // 默认状态下无阴影
                color: theme.palette.text.primary, // 使用主题的主文本颜色
                backgroundColor: theme.palette.background.paper, // 使用主题的背景颜色
            }}
          />
          {sendButtonDisable ?
          <Box sx={{ bottom: 3, right: 0, position: 'absolute', whiteSpace: 'nowrap' }} >
            <CircularProgress size={20} color="inherit"/>
          </Box>
          :
          <Button type='submit' variant='contained' disabled={sendButtonDisable}  sx={{ bottom: -1.5, right: 0, position: 'absolute', whiteSpace: 'nowrap' }} >
            {sendButtonText}
          </Button>
          }

        </Box>
      </Form>
    </Box>
  )
}

export default SendMsgForm
