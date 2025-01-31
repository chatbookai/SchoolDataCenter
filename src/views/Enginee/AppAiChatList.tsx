// ** React Imports
import { Fragment, useState } from 'react'

// ** MUI Imports
import Box from '@mui/material/Box'
import Grid from '@mui/material/Grid'
import Button from '@mui/material/Button'
import CardMedia from '@mui/material/CardMedia'
import Dialog from '@mui/material/Dialog'
import DialogContent from '@mui/material/DialogContent'
import Typography from '@mui/material/Typography'
import ChatIndex from 'src/views/AiChat/ChatIndex'

import Container from '@mui/material/Container'
import CircularProgress from '@mui/material/CircularProgress'
import { useTheme } from '@mui/material/styles'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

import { useTranslation } from 'react-i18next'

const AppAiChatList = (props: any) => {
  // ** Hook
  const { t } = useTranslation()
  const theme = useTheme()

  // ** Props
  const {
    store,
    authConfig,
    loading,
    loadingText,
    show,
    setShow,
    setAddEditActionId,
    setViewActionOpen,
    setEditViewCounter,
    setAddEditActionName,
    isMobileData,
    setCSRF_TOKEN
  } = props

  const [pageModel, setPageModel] = useState<string>('Main')
  const [app, setApp] = useState<any>(null)

  const [historyCounter, setHistoryCounter] = useState<number>(0)

  const QuestionGuideTemplate = '你是一个AI智能助手，可以回答和解决我的问题。请结合前面的对话记录，以用户自己的角度，帮用户生成 3 个问题，用于引导用户来进行继续提问。要求提问的角度要站在用户的角度生成提问句子。问题的长度应小于20个字符，要求使用UTF-8编码，按 JSON 格式返回: ["问题1", "问题2", "问题3"]'

  const handleSetChatWithApp = async (item: any) => {
    setPageModel("ChatWithApp")
    setApp({...item, id: "ChatApp-" + item.id, AppName2: item.AppModel, avatar: '1.png', Model: {}, QuestionGuideTemplate })
  }

  const AppList = store.init_default.data.map( (Item: any) => ({...Item, id: Item.id2}) )

  const renderContent = () => {
      return (
        <Grid container>
          {pageModel == "Main" && (
            <Grid item xs={12} sx={{ height: '100%', overflowY: 'auto', scrollbarWidth: 'thin', scrollbarColor: '#ffffff' }}>
              <Fragment>
                {isMobileData == false && (
                  <Grid container spacing={2}>
                    <Grid item xs={12}>
                      <Box px={2} pb={0}>
                        <Typography variant="h6">AI对话</Typography>
                      </Box>
                    </Grid>
                  </Grid>
                )}
                <Grid container spacing={2} sx={{ my: 0}}>
                  {AppList && AppList.map((item: any, index: number) => (
                    <Grid item key={index} xs={12} sm={6} md={4} lg={4}>
                      <Box position="relative">
                        <CardMedia image={`/images/cardmedia/cardmedia-${theme.palette.mode}.png`} sx={{ height: '12rem', objectFit: 'contain', borderRadius: 1 }}/>
                        <Box position="absolute" top={10} left={3} m={1} px={0.8} borderRadius={1} >
                          <Box display="flex" alignItems="center">
                            <Icon icon={item.AppAvatar} color={theme.palette.primary.main} fontSize={35} />
                            <Typography
                                sx={{
                                    fontWeight: 500,
                                    lineHeight: 1.71,
                                    letterSpacing: '0.22px',
                                    fontSize: '1rem !important',
                                    overflow: 'hidden',
                                    textOverflow: 'ellipsis',
                                    whiteSpace: 'nowrap',
                                    flexGrow: 1,
                                    ml: 2
                                }}
                            >
                                {item.AppName}
                            </Typography>
                          </Box>
                        </Box>
                        <Box position="absolute" top={55} left={5} m={1} px={0.8} borderRadius={1}
                          sx={{
                            overflow: 'hidden',
                            display: '-webkit-box',
                            WebkitLineClamp: 5,
                            WebkitBoxOrient: 'vertical',
                          }}
                          >
                          <Typography variant='caption'>{item.AppIntro}</Typography>
                        </Box>
                        <Box position="absolute" bottom={0} left={1} m={1} px={0.8}>
                          <Button
                            variant="text"
                            size="small"
                            startIcon={<Icon icon={item.permission == 'private' ? 'ri:git-repository-private-line' : 'material-symbols:share'} />}
                            onClick={()=>{
                              setAddEditActionId(item.id)
                              setViewActionOpen(true)
                              setEditViewCounter(0)
                              setCSRF_TOKEN(store.init_default.CSRF_TOKEN)
                              setAddEditActionName('view_default')
                            }}
                          >
                            {'查看详情'}
                          </Button>
                        </Box>
                        <Box position="absolute" bottom={0} right={1} m={1} px={0.8}>
                          <Button variant="text" size="small" startIcon={<Icon icon='material-symbols:chat-outline' />}
                             onClick={()=>handleSetChatWithApp(item)}
                            >
                            {'开始使用'}
                          </Button>
                        </Box>
                      </Box>
                    </Grid>
                  ))}
                  {store.init_default.data && store.init_default.data.length == 0 && loading == false?
                  <Grid
                    item
                    key='0'
                    xs={12}
                    sx={{
                      textAlign: 'center',
                      marginTop: '1rem',
                      fontSize: '1.2rem',
                      fontWeight: 'bold',
                      color: 'gray',
                    }}
                  >
                    <Typography variant="body1">{t('No Data')}</Typography>
                  </Grid>
                  :
                  null}
                </Grid>
              </Fragment>
              {loading ?
              <Dialog
                open={show}
                onClose={() => setShow(false)}
              >
                <DialogContent sx={{ position: 'relative' }}>
                  <Container>
                    <Grid container spacing={2}>
                      <Grid item xs={8} sx={{}}>
                        <Box sx={{ ml: 6, display: 'flex', alignItems: 'center', flexDirection: 'column', whiteSpace: 'nowrap' }}>
                          <CircularProgress sx={{ mb: 4 }} />
                          <Typography>{t(loadingText) as string}</Typography>
                        </Box>
                      </Grid>
                    </Grid>
                  </Container>
                </DialogContent>
              </Dialog>
              :
              null}
            </Grid>
          )}
          {pageModel == "ChatWithApp" && (
            <Fragment>
              <ChatIndex authConfig={authConfig} app={app} historyCounter={historyCounter} setHistoryCounter={setHistoryCounter} setPageModel={setPageModel}/>
            </Fragment>
          )}
        </Grid>
      )
  }

  return renderContent()
}

AppAiChatList.contentHeightFixed = true

export default AppAiChatList
