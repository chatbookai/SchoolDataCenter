// ** React Imports
import { useRef, useEffect, Fragment, useState } from 'react'
import { saveAs } from 'file-saver';

// ** MUI Imports
import Box from '@mui/material/Box'
import Button from '@mui/material/Button'
import { styled } from '@mui/material/styles'
import Typography from '@mui/material/Typography'
import CardMedia from '@mui/material/CardMedia'
import Link from 'next/link'
import toast from 'react-hot-toast'
import Avatar from '@mui/material/Avatar'
import ListItem from '@mui/material/ListItem';
import IconButton from '@mui/material/IconButton'
import Tooltip from '@mui/material/Tooltip'
import { useTranslation } from 'react-i18next'
import CircularProgress from '@mui/material/CircularProgress'

import ChatContextPreview from 'src/views/AiChat/ChatContextPreview'

import { AppAvatar } from 'src/functions/ChatBook'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// ** Custom Components Imports
import CustomAvatar from 'src/@core/components/mui/avatar'

import ReactMarkdown from 'react-markdown'
import RemarkBreaks from "remark-breaks";

import SendMsgForm from 'src/views/AiChat/SendMsgForm'

import ModuleTableData from 'src/views/AiChat/module/TableData'
import ModuleMsgData from 'src/views/AiChat/module/MsgData'


// ** Types Imports
import {
  MessageType,
  ChatLogChatType,
  MessageGroupType,
  FormattedChatsType
} from 'src/types/apps/chatTypes'

const LinkStyled = styled(Link)(({ theme }) => ({
  textDecoration: 'none',
  color: theme.palette.success.main
}))

const SystemPromptTemplate = ({text, handleSendMsg}: any) => {

  const handleClick = (event: any, keyword: string) => {
    event.preventDefault();
    handleSendMsg(keyword)
  };

  const replaceKeywordsWithLinks = (text: string) => {
    let ListItemStatus = false //当开始处理[问题]以后,就不需要做换行操作了
    const replacedText = text.split(/(\[.*?\]|\n)/).map((part, index) => {
      if (part.startsWith('[') && part.endsWith(']')) {
        ListItemStatus = true
        const keyword = part.slice(1, -1);

        return (
          <ListItem key={index} sx={{ m: 0, p: 0, pt: 0 }}>
            <Typography sx={{ mr: 3, my: 0.5 }}>•</Typography>
            <LinkStyled href="#" onClick={(event: any) => handleClick(event, keyword)}>
              {keyword}
            </LinkStyled>
          </ListItem>
        );
      }
      else if (part === '\n' && ListItemStatus == false) {
        return <br key={index} />;
      }

      return part;
    });

    return replacedText;
  };

  //const text = '你好，我是知识库助手，请不要忘记选择知识库噢~[你是谁]你好: [如何使用]';
  const processedText = replaceKeywordsWithLinks(text);

  return (
    <Box sx={{ pt: 2 }}>
      {processedText}
    </Box>
  );
};

const ChatLog = (props: any) => {
  // ** Props
  const { t } = useTranslation()
  const { authConfig, data, chatName, app, sendButtonDisable, handleDeleteOneChatLogById, sendMsg, store, questionGuide, setClearButtonClickEvent, setPageModel,
          sendButtonLoading, sendButtonText, sendInputText, rowInMsg, handleSetRowInMsg, maxRows, setStopMsg, stepingMessage
        } = props

  const handleSendMsg = (msg: string) => {
    if (store && store.selectedChat && msg.trim().length) {
      sendMsg({ ...store.selectedChat, message: msg, template: '' })
    }
  }

  const [contextPreviewOpen, setContextPreviewOpen] = useState<boolean>(false)
  const [contextPreviewData, setContextPreviewData] = useState<any[]>([])

  // ** Ref
  const chatArea = useRef(null)


  // ** Scroll to chat bottom
  const scrollToBottom = () => {

    if (chatArea.current && sendButtonDisable == false) {
      // @ts-ignore
      chatArea.current.scrollTop = Number.MAX_SAFE_INTEGER;
    }

  }

  const handleDownload = (DownloadUrl: string, FileName: string) => {
    fetch(DownloadUrl)
      .then(response => response.blob())
      .then(blob => {
        saveAs(blob, FileName);
      })
      .catch(error => {
        console.log('Error downloading file:', error);
      });
  };

  // ** Formats chat data based on sender
  const formattedChatData = () => {
    let chatLog: MessageType[] | [] = []
    if (data.chat) {
      chatLog = data.chat.chat
    }

    const formattedChatLog: FormattedChatsType[] = []
    let chatMessageSenderId = chatLog[0] ? chatLog[0].senderId : 11
    let msgGroup: MessageGroupType = {
      senderId: chatMessageSenderId,
      messages: []
    }
    chatLog.forEach((msg: any, index: number) => {
      if (chatMessageSenderId === msg.senderId) {
        msgGroup.messages.push({
          time: msg.time,
          msg: msg.message,
          responseTime: msg.responseTime,
          chatlogId: msg.chatlogId,
          history: msg.history,
          feedback: msg.feedback,
          question: ''
        })
      } else {
        chatMessageSenderId = msg.senderId

        formattedChatLog.push(msgGroup)
        msgGroup = {
          senderId: msg.senderId,
          messages: [
            {
              time: msg.time,
              msg: msg.message,
              responseTime: msg.responseTime,
              chatlogId: msg.chatlogId,
              history: msg.history,
              feedback: msg.feedback,
              question: msg.question
            }
          ]
        }
      }

      if (index === chatLog.length - 1) formattedChatLog.push(msgGroup)
    })

    return formattedChatLog
  }

  useEffect(() => {

    // 使用 requestAnimationFrame 延迟执行滚动操作
    const scroll = () => {
      if (chatArea.current && sendButtonDisable) {

        // @ts-ignore
        chatArea.current.scrollTop = chatArea.current.scrollHeight;
      }
    };
    requestAnimationFrame(scroll);
  }, [formattedChatData()]);

  useEffect(() => {
    if (data && sendButtonDisable == false) {
      scrollToBottom()
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [data, questionGuide, sendButtonDisable])


  return (
    <Box
        className='app-chat'
        sx={{
          width: '100%',
          height: '100%',
          display: 'flex',
          borderRadius: 1,
          overflowX: 'hidden',
          overflowY: 'auto',
          position: 'relative',
          flexDirection: 'column'
        }}
      >
      <Box display="flex" alignItems="center" justifyContent="left" borderRadius="8px" px={3} my={1} >
        <CustomAvatar
          skin='light'
          color={'primary'}
          sx={{
            width: '2rem',
            height: '2rem',
            fontSize: '0.875rem',
          }}
          {...{
            src: app.avatar? AppAvatar(authConfig.backEndApiHost, app.avatar) : '/images/avatars/1.png',
            alt: chatName
          }}
        >
        </CustomAvatar>
        <Typography
          sx={{
            width: 'fit-content',
            fontSize: '0.875rem',
            p: theme => theme.spacing(0.5, 2, 0.5, 2),
            ml: 1,
            color: 'text.primary',
          }}
          >
            {chatName}
        </Typography>
        {sendButtonDisable == true ?
        <Fragment>
          <Typography
            sx={{
              boxShadow: 1,
              borderRadius: 0.5,
              width: 'fit-content',
              fontSize: '0.875rem',
              p: theme => theme.spacing(0.5, 2, 0.5, 2),
              ml: 1,
              color: 'text.primary',
              backgroundColor: 'background.paper'
            }}
            >
              <CircularProgress color='success' size={10} sx={{mr: 1}}/>
              {t('AI 对话')}
          </Typography>
        </Fragment>
        :
        null
        }
        <Box sx={{ marginLeft: 'auto' }}>
          <IconButton aria-label='capture screenshot' color='secondary' size='small' onClick={()=>{
            setClearButtonClickEvent(true)
          }}>
            <Icon icon='lineicons:trash-3' fontSize='inherit' />
          </IconButton>
          <IconButton aria-label='capture screenshot' color='secondary' onClick={()=>{
            setPageModel("Main")
          }}>
            <Icon icon='ic:twotone-keyboard-arrow-left' fontSize='inherit' />
          </IconButton>
        </Box>
      </Box>
      <Box ref={chatArea} sx={{ height: '100%', width: '100%', p: 3, pb: 6, overflowY: 'auto', overflowX: 'hidden' }}>
        {
        formattedChatData().map((item: FormattedChatsType, index: number, ChatItemMsgList: any[]) => {
          const isSender = item.senderId === data.userContact.id

          return (
            <Box
              key={index}
              sx={{
                width: '100%',
                display: 'flex',
                flexDirection: 'column',
                mb: index !== ChatItemMsgList.length - 1 ? 4 : undefined
              }}
            >
              {isSender == true && (
                <Box display="flex" alignItems="center" justifyContent="right" borderRadius="8px" p={0} mb={1} >
                  <Tooltip title={t('复制')}>
                    <IconButton aria-label='capture screenshot' color='secondary' size='small' onClick={()=>{
                      navigator.clipboard.writeText(item.messages[0].msg);
                      toast.success(t('复制成功') as string, { duration: 1000 })
                    }}>
                      <Icon icon='material-symbols:file-copy-outline-rounded' fontSize='inherit' />
                    </IconButton>
                  </Tooltip>
                  <Tooltip title={t('重新生成')}>
                    <IconButton aria-label='capture screenshot' color='secondary' size='small' onClick={()=>{
                      handleSendMsg(item.messages[0].msg)
                    }}>
                      <Icon icon='mdi:refresh' fontSize='inherit' />
                    </IconButton>
                  </Tooltip>
                  <Tooltip title={t('删除')}>
                    <IconButton aria-label='capture screenshot' color='secondary' size='small' onClick={()=>{
                      handleDeleteOneChatLogById(item.messages[0].chatlogId)
                    }}>
                      <Icon icon='mdi:trash-outline' fontSize='inherit' />
                    </IconButton>
                  </Tooltip>
                  <CustomAvatar
                    skin='light'
                    color={'primary'}
                    sx={{
                      width: '2rem',
                      height: '2rem',
                      fontSize: '0.875rem',
                    }}
                    {...{
                      src: data.userContact.avatar,
                      alt: data.userContact.fullName
                    }}
                  >
                    {app.name}
                  </CustomAvatar>
                </Box>
              )}

              {isSender == false && index > 0 && (
                <Box display="flex" alignItems="center" justifyContent="left" borderRadius="8px" p={0} mb={1} >
                  <CustomAvatar
                    skin='light'
                    color={'primary'}
                    sx={{
                      width: '2rem',
                      height: '2rem',
                      fontSize: '0.875rem',
                    }}
                    {...{
                      src: app.avatar? AppAvatar(authConfig.backEndApiHost, app.avatar) : '/images/avatars/1.png',
                      alt: chatName
                    }}
                  >
                  </CustomAvatar>
                  <Typography
                    sx={{
                      width: 'fit-content',
                      fontSize: '0.875rem',
                      p: theme => theme.spacing(0.5, 2, 0.5, 2),
                      ml: 1,
                      color: 'text.primary',
                    }}
                    >
                      {chatName}
                  </Typography>
                  {sendButtonDisable == true && index == ChatItemMsgList.length - 1  ?
                  <Fragment>
                    <Typography
                      sx={{
                        boxShadow: 1,
                        borderRadius: 0.5,
                        width: 'fit-content',
                        fontSize: '0.875rem',
                        p: theme => theme.spacing(0.5, 2, 0.5, 2),
                        ml: 1,
                        color: 'text.primary',
                        backgroundColor: 'background.paper'
                      }}
                      >
                        <CircularProgress color='success' size={10} sx={{mr: 1}}/>
                        {t('AI 对话')}
                    </Typography>
                  </Fragment>
                  :
                  null
                  }
                  {(sendButtonDisable == true && index < ChatItemMsgList.length - 1) || (sendButtonDisable == false && index > 0) ?
                  <Fragment>
                    <Tooltip title={t('复制')}>
                      <IconButton aria-label='capture screenshot' color='secondary' size='small' onClick={()=>{
                        navigator.clipboard.writeText(item.messages[0].msg);
                        toast.success(t('复制成功') as string, { duration: 1000 })
                      }}>
                        <Icon icon='material-symbols:file-copy-outline-rounded' fontSize='inherit' />
                      </IconButton>
                    </Tooltip>

                  </Fragment>
                  :
                  null
                  }
                </Box>
              )}

              <Box className='chat-body' sx={{ width: '100%' }}>
                {item.messages.map((chat: ChatLogChatType, ChatIndex: number) => {
                  let ChatMsgType = 'Chat'
                  let ChatMsgContent: any
                  if(chat.msg.includes('"type":"image"')) {
                    ChatMsgType = 'Image'
                    ChatMsgContent = JSON.parse(chat.msg)
                  }
                  if(chat.msg.includes('"type":"audio"')) {
                    ChatMsgType = 'Audio'
                    ChatMsgContent = JSON.parse(chat.msg)
                  }

                  return (
                    <Box key={ChatIndex} sx={{ '&:not(:last-of-type)': { mb: 3 } }}>
                        {ChatMsgType == "Chat" &&
                          <Fragment>
                            <Typography sx={{
                                          boxShadow: 1,
                                          borderRadius: 1,
                                          width: isSender ? 'fit-content' : '100%',
                                          fontSize: '0.875rem',
                                          p: theme => theme.spacing(0.1, 2, 0.1, 2.5),
                                          ml: isSender ? 'auto' : undefined,
                                          borderTopLeftRadius: !isSender ? 0 : undefined,
                                          borderTopRightRadius: isSender ? 0 : undefined,
                                          color: isSender ? 'common.white' : 'text.primary',
                                          backgroundColor: isSender ? 'primary.main' : 'background.paper'
                                        }}
                            >
                              { index == 0 && (
                                <SystemPromptTemplate text={chat.msg} handleSendMsg={handleSendMsg}/>
                              )}

                              { index > 0 && chat.msg && chat.msg.includes('"module":"msg"') && (
                                <ModuleMsgData data={chat.msg} />
                              )}

                              { index > 0 && chat.msg && chat.msg.includes('"module":"module"') && (
                                <ModuleMsgData data={chat.msg} />
                              )}

                              { index > 0 && chat.msg && !chat.msg.includes('"module":"') && (
                                <ReactMarkdown remarkPlugins={[RemarkBreaks]}>{chat.msg.replaceAll("\\\\\n", '  \n').replaceAll("\\\\n", '  \n').replaceAll("\\\n", '  \n').replaceAll("\\n", '  \n')}</ReactMarkdown>
                              )}

                              {!isSender && index == ChatItemMsgList.length - 1 && index>0 && questionGuide ?
                                <Box>
                                  <Box display="flex" alignItems="center">
                                    <Avatar src={'/images/aichat/cq.png'} sx={{ mr: 2.5, width: 26, height: 26 }} />
                                    {t('相关问题')}
                                  </Box>
                                  {questionGuide && questionGuide.length > 0 && Array.isArray(questionGuide) && questionGuide.map((question: string, index: number)=>{

                                    return (
                                      <ListItem key={index} sx={{m: 0, p: 0, pt: 0}}>
                                        <Typography sx={{mr: 3, my: 0.5, ml: 5  }}>•</Typography>
                                        {question != 'Generating, please wait...' ?
                                        <LinkStyled href="#" onClick={() => {
                                          handleSendMsg(question)
                                        }}>
                                          {question}
                                        </LinkStyled>
                                        :
                                        <Typography sx={{my: 0.5  }} variant='body2'>{t('正在为您生成近似的三个相关问题,请稍等...')}</Typography>
                                        }

                                      </ListItem>
                                    )
                                  })}
                                </Box>
                              :
                              null
                              }
                            </Typography>

                            { index > 0 && chat.msg && chat.msg.includes('"module":"table"') && (
                              <ModuleTableData data={chat.msg} />
                            )}

                            {!isSender && Number(chat.responseTime) > 0 && ( (index + 1 == ChatItemMsgList.length && !sendButtonDisable) || (index + 1 < ChatItemMsgList.length))?
                            <Typography sx={{
                                          boxShadow: 1,
                                          borderRadius: 1,
                                          width: isSender ? 'fit-content' : '100%',
                                          fontSize: '0.875rem',
                                          p: theme => theme.spacing(0.1, 2, 0.1, 2.5),
                                          ml: isSender ? 'auto' : undefined,
                                          borderTopLeftRadius: !isSender ? 0 : undefined,
                                          borderTopRightRadius: isSender ? 0 : undefined,
                                          color: isSender ? 'common.white' : 'text.primary',
                                          backgroundColor: isSender ? 'primary.main' : 'background.paper'
                                        }}
                            >
                              <Box
                                  sx={{
                                    mt: 1,
                                    ml: -2.5,
                                    display: 'flex',
                                    alignItems: 'center',
                                    justifyContent: isSender ? 'flex-end' : 'flex-start'
                                  }}
                                >
                                  <Box display="flex" alignItems="center" justifyContent="left" borderRadius="8px" p={0} mb={1} >
                                      <Tooltip title={t('查看明细')}>
                                        <Button color='success' size="small" style={{ whiteSpace: 'nowrap' }} onClick={()=>{
                                          const historyAll: any[] = [...chat.history]
                                          historyAll.push([chat.question, chat.msg])
                                          setContextPreviewOpen(true)
                                          setContextPreviewData(historyAll)
                                        }}>
                                          {t('对话数量')}({(chat.history.length+1)*2+1})
                                        </Button>
                                      </Tooltip>
                                      <Tooltip title={t('运行时间')}>
                                        <Button color='error' size="small" style={{ whiteSpace: 'nowrap' }} disableTouchRipple disableRipple>{chat.responseTime}S</Button>
                                      </Tooltip>
                                      <Button color='info' size="small" disabled style={{ whiteSpace: 'nowrap' }}>
                                      {chat.time ? new Date(Number(chat.time)).toLocaleString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true }) : null}
                                      </Button>
                                  </Box>
                              </Box>
                            </Typography>
                            :
                            null
                            }
                          </Fragment>
                        }
                        {ChatMsgType == "Image" && ChatMsgContent && ChatMsgContent.ShortFileName ?
                          <Fragment>
                            <LinkStyled target='_blank' href={authConfig.backEndApiHost + 'images/' + ChatMsgContent.ShortFileName}>
                              <CardMedia image={authConfig.backEndApiHost + 'images/' + ChatMsgContent.ShortFileName} sx={{ mt: 1, width: '500px', height: '500px', borderRadius: '5px' }}/>
                            </LinkStyled>
                            <Box
                                sx={{
                                  mt: 1,
                                  display: 'flex',
                                  alignItems: 'center',
                                  justifyContent: isSender ? 'flex-end' : 'flex-start'
                                }}
                              >
                                <Typography variant='caption'>
                                  {chat.time
                                    ? new Date(Number(chat.time)).toLocaleString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true })
                                    : null}
                                  {ChatMsgContent.ShortFileName ?
                                  <LinkStyled onClick={()=>handleDownload(authConfig.backEndApiHost + 'images/' + ChatMsgContent.ShortFileName, ChatMsgContent.ShortFileName + '.png')} href={'#'} sx={{ml: 1}}>
                                    Download
                                  </LinkStyled>
                                  :
                                  null
                                  }
                                </Typography>
                              </Box>
                          </Fragment>
                          :
                          null
                        }
                        {ChatMsgType == "Audio" && ChatMsgContent && ChatMsgContent.ShortFileName ?
                          <Fragment>
                            <LinkStyled target='_blank' href={authConfig.backEndApiHost + 'api/audio/' + ChatMsgContent.ShortFileName}>
                              <CardMedia component="audio" controls src={authConfig.backEndApiHost + 'api/audio/' + ChatMsgContent.ShortFileName} sx={{ mt: 1, width: '360px', borderRadius: '5px' }}/>
                            </LinkStyled>
                            <Box
                                sx={{
                                  mt: 1,
                                  display: 'flex',
                                  alignItems: 'center',
                                  justifyContent: isSender ? 'flex-end' : 'flex-start'
                                }}
                              >
                                <Typography variant='caption'>
                                  {chat.time
                                    ? new Date(Number(chat.time)).toLocaleString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true })
                                    : null}
                                  {ChatMsgContent.ShortFileName ?
                                  <LinkStyled onClick={()=>handleDownload(authConfig.backEndApiHost + 'api/audio/' + ChatMsgContent.ShortFileName, ChatMsgContent.ShortFileName + '.mp3')} href={'#'} sx={{ml: 1}}>
                                    Download
                                  </LinkStyled>
                                  :
                                  null
                                  }
                                </Typography>
                              </Box>
                          </Fragment>
                          :
                          null
                        }
                    </Box>
                  )
                })}
              </Box>

            </Box>
          )
        })
        }
        {sendButtonDisable == true && app.AppName == "AI智能仪表盘" && (
          <Box display="flex" alignItems="center" justifyContent="left" borderRadius="8px" p={0} mb={1} pt={3} >
            <CustomAvatar
              skin='light'
              color={'primary'}
              sx={{
                width: '2rem',
                height: '2rem',
                fontSize: '0.875rem',
              }}
              {...{
                src: app.avatar? AppAvatar(authConfig.backEndApiHost, app.avatar) : '/images/avatars/1.png',
                alt: chatName
              }}
            >
            </CustomAvatar>
            <Typography
              sx={{
                width: 'fit-content',
                fontSize: '0.875rem',
                p: theme => theme.spacing(0.5, 2, 0.5, 2),
                ml: 1,
                color: 'text.primary',
              }}
              >
                {chatName}
            </Typography>
            <Fragment>
              <Typography
                sx={{
                  boxShadow: 1,
                  borderRadius: 0.5,
                  width: 'fit-content',
                  fontSize: '0.875rem',
                  p: theme => theme.spacing(0.5, 2, 0.5, 2),
                  ml: 1,
                  color: 'text.primary',
                  backgroundColor: 'background.paper'
                }}
                >
                  <CircularProgress color='success' size={10} sx={{mr: 1}}/>
                  {stepingMessage}
              </Typography>
            </Fragment>
          </Box>
        )}
      </Box>
      <Box sx={{
              mt: 'auto', // 自动填充剩余空间
              display: 'flex',
              alignItems: 'center',
              justifyContent: 'left',
              borderRadius: '8px',
              px: 3,
              my: 1,
              backgroundColor: 'background.paper', // 设置背景颜色
              zIndex: 1000, // 确保在其他内容之上
            }}>
        <SendMsgForm authConfig={authConfig} store={store} sendMsg={sendMsg} sendButtonDisable={sendButtonDisable} sendButtonLoading={sendButtonLoading} sendButtonText={sendButtonText} sendInputText={sendInputText} rowInMsg={rowInMsg} handleSetRowInMsg={handleSetRowInMsg} maxRows={maxRows} setStopMsg={setStopMsg} />
      </Box>
      <ChatContextPreview contextPreviewOpen={contextPreviewOpen} setContextPreviewOpen={setContextPreviewOpen} contextPreviewData={contextPreviewData} app={app}/>
    </Box>
  )
}

export default ChatLog
