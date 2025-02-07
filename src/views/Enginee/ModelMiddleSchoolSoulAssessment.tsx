import { useState, useEffect, useRef, Fragment } from 'react'
import { Filesystem, Directory, Encoding } from '@capacitor/filesystem';

// ** MUI Imports
import Button from '@mui/material/Button'
import Grid from '@mui/material/Grid'
import Card from '@mui/material/Card'
import Table from '@mui/material/Table'
import Divider from '@mui/material/Divider'
import TableRow from '@mui/material/TableRow'
import TableHead from '@mui/material/TableHead'
import TableBody from '@mui/material/TableBody'
import Typography from '@mui/material/Typography'
import Box from '@mui/material/Box'
import CardContent from '@mui/material/CardContent'
import { styled, useTheme } from '@mui/material/styles'
import TableContainer from '@mui/material/TableContainer'
import TableCell, { TableCellBaseProps } from '@mui/material/TableCell'

import ReactApexcharts from 'src/@core/components/react-apexcharts'

import { ApexOptions } from 'apexcharts'
import ReactMarkdown from 'react-markdown'
import {isMobile} from 'src/configs/functions'

import Link from 'next/link'
import { defaultConfig } from 'src/configs/auth'
import axios from 'axios'

import html2canvas from 'html2canvas'

import { Capacitor } from '@capacitor/core'
import { Share } from '@capacitor/share'

import Icon from 'src/@core/components/icon'
import { Dialog, DialogTitle, DialogContent, DialogActions, } from '@mui/material';
import SaveIcon from '@mui/icons-material/Save';
import MoreHorizIcon from '@mui/icons-material/MoreHoriz';

import CircularProgress from '@mui/material/CircularProgress'
import Backdrop from '@mui/material/Backdrop'

const platform = Capacitor.getPlatform();

interface Props {
  authConfig: any
  dataOriginal: any
  modelOriginal: string
  id: string
  backEndApi: string
  viewPageShareStatus: boolean | undefined
  handSetViewPageShareStatus?: any
}

const MUITableCell = styled(TableCell)<TableCellBaseProps>(({ theme }) => ({
  borderBottom: 0,
  paddingLeft: '0 !important',
  paddingRight: '0 !important',
  paddingTop: `${theme.spacing(1)} !important`,
  paddingBottom: `${theme.spacing(1)} !important`
}))

const ModelMiddleSchoolSoulAssessment = ({ authConfig, dataOriginal, modelOriginal, id, backEndApi, viewPageShareStatus, handSetViewPageShareStatus }: Props) => {
  // ** Hook
  const theme = useTheme()

  const [data, setData] = useState<any>(dataOriginal)
  const [model, setModel] = useState<any>(modelOriginal)
  const [printModel, setPrintModel] = useState<string>("print")

  const isMobileData = isMobile()

  const printRef = useRef<HTMLDivElement>(null);

  const [loading, setLoading] = useState<boolean>(false)

  const handleDownloadImage = async () => {

    try {
      const element = printRef.current;
      const canvas = await html2canvas(element as HTMLElement, {
          scale: 2, // 提高分辨率
          useCORS: true, // 允许跨域图片
          allowTaint: false, // 不允许跨域图片污染
      });
      const imageBase64Text = canvas.toDataURL('image/png');

      if (platform === 'android' || platform === 'ios') {
        // 将 dataURL 转换为 Blob
        const response = await fetch(imageBase64Text);
        const blob = await response.blob();

        // 将 Blob 转换为 ArrayBuffer
        const arrayBuffer = await blob.arrayBuffer();
        const uint8Array = new Uint8Array(arrayBuffer);

        // 保存文件到设备的本地存储
        const fileName = `psychological_report_${Date.now()}.png`;
        const fileBlob = new Blob([uint8Array], { type: 'image/png' });
        const savedFile = await Filesystem.writeFile({
          path: fileName,
          data: fileBlob,
          directory: Directory.Documents,
        });
        console.log('File saved:', savedFile);
        const fileUrl = savedFile.uri;
        const link = document.createElement('a');
        link.href = fileUrl;
        link.download = fileName;
        link.textContent = '点击下载图片 android ios';
        document.body.appendChild(link);
      }

      if (platform === 'web') {
        if (!previewImage) return;
        const fileName = `${data['测评名称']}(${data['用户信息']['姓名']}-${data['用户信息']['测评时间']}).png`;
        const link = document.createElement('a');
        link.href = previewImage;
        link.download = fileName;
        link.click();
      }

    } catch (error) {
        console.error('Error generating or saving image:', error);
    }

  };

  const handleShareImageToOther = async () => {

    const element = printRef.current;
    const canvas = await html2canvas(element as HTMLElement, {
      scale: 2, // 提高分辨率
      useCORS: true, // 允许跨域图片
      allowTaint: false, // 不允许跨域图片污染
    });

    const imageBase64 = canvas.toDataURL('image/png');

    // 2. 将 Base64 数据转为二进制字符串
    const base64Data = imageBase64.split(',')[1];

    // 3. 使用 Filesystem 保存图片到本地
    const fileName = `shared-image-${Date.now()}.png`;
    const fileResult = await Filesystem.writeFile({
      path: fileName,
      data: base64Data,
      directory: Directory.Cache, // 缓存目录，适合临时文件
      encoding: Encoding.UTF8,
    });

    // 4. 获取文件路径（Capacitor 文件 URL）
    const filePath = fileResult.uri;

    if (platform === 'android' || platform === 'ios') {

      // 5. 使用 Share 插件分享文件
      await Share.share({
        title: filePath,
        text: filePath,
        url: filePath, // 分享文件路径
        dialogTitle: '分享图片',
      });

    }

    //URL.revokeObjectURL(blobUrl);

  };

  useEffect(() => {
    //打印页面时,需要单独再获取一次API的内容
    //const backEndApi = 'apps/apps_378.php'
    const action = 'view_default'
    const storedToken = window.localStorage.getItem(defaultConfig.storageTokenKeyName)!
    if (id && id.length > 32 && data == null) {
      axios
        .get(authConfig.backEndApiHost + backEndApi, { headers: { Authorization: storedToken+"::::" }, params: { action, id, isMobileData: false } })
        .then(res => {
          const data = res.data
          if(data && data.model) {
            setData(data.data)
            setModel(data.model)
            setPrintModel("")
            console.log("data.model", data, model);
          }
        })
        .catch(() => {
          console.log("axios.get editUrl return")
        })
    }
  }, [id])

  const series = data && data['测评分析'] ? [{name: '心理测评', data: Object.values(data['测评分析']) as number[]}] : [{name: '心理测评', data: []}]

  const options: ApexOptions = {
    chart: {
      parentHeightOffset: 0,
      toolbar: { show: false }
    },
    colors: [theme.palette.primary.main, theme.palette.info.main],
    plotOptions: {
      radar: {
        size: 110,
        polygons: {
          connectorColors: theme.palette.divider,
          strokeColors: [
            theme.palette.divider,
            'transparent',
            'transparent',
            'transparent',
            'transparent',
            'transparent'
          ]
        }
      }
    },
    fill: {
      type: 'gradient',
      gradient: {
        shade: 'dark',
        gradientToColors: [theme.palette.primary.main, theme.palette.info.main],
        shadeIntensity: 1,
        type: 'vertical',
        opacityFrom: 1,
        opacityTo: 0.9,
        stops: [0, 100]
      }
    },
    labels: data && data['测评分析'] ? Object.keys(data['测评分析']) as string[] : [],
    markers: { size: 0 },
    legend: {
      labels: { colors: theme.palette.text.secondary }
    },
    grid: { show: false },
    xaxis: {
      labels: {
        show: true,
        style: {
          fontSize: '14px',
          colors: [
            theme.palette.text.disabled,
            theme.palette.text.disabled,
            theme.palette.text.disabled,
            theme.palette.text.disabled,
            theme.palette.text.disabled,
            theme.palette.text.disabled
          ]
        }
      }
    },
    yaxis: { show: false }
  }

  useEffect(() => {
    if ( series && series[0] && series[0].data && series[0].data.length > 0 && id && id.length > 32 && data && printModel == "" ) {
      setTimeout(() => {
        window.print()
      }, 3000)
    }
  }, [id, data, series])

  const chartRef = useRef(null);

  useEffect(() => {
    if (chartRef.current) {
      const apexChart = chartRef.current as any;
      if(series && series[0] && series[0].data && series[0].data.length > 0 && apexChart && apexChart.chart) {
        apexChart.chart.render();
      }
    }
  }, [series, options]);

  const [previewImage, setPreviewImage] = useState<string | null>(null);

  const handleGenerateShareImage = async () => {
    setLoading(true)
    if (!printRef.current) return;
    const canvas = await html2canvas(printRef.current, {
      scale: 2,
      useCORS: true,
      allowTaint: false,
    });
    const dataURL = canvas.toDataURL('image/png');
    setPreviewImage(dataURL);
    setLoading(false)
  };

  const handleShareToWeChat = async () => {
    if (!previewImage) return;
    try {
      if (platform === 'android' || platform === 'ios') {
        await Share.share({
          title: 'Check out this image!',
          text: 'I generated this image using my app.',
          url: previewImage,
          dialogTitle: 'Share this image',
        });
      }
    } catch (error) {
      console.error('Error sharing:', error);
    }
  };

  interface ShareDialogProps {
    previewImage: string | null;
    onClose: () => void;
    onSave: () => void;
    onShareToWeChat: () => void;
    onMoreOptions: () => void;
  }

  const ShareDialog: React.FC<ShareDialogProps> = ({
    previewImage,
    onClose,
    onSave,
    onShareToWeChat,
    onMoreOptions,
  }) => {
    return (
      <Fragment>
        {data && data.sharedModel && data.sharedModel.title && (
          <Dialog open={!!previewImage} onClose={onClose} sx={{mt: 10, mx: 5}}>
            <DialogTitle sx={{ textAlign: 'left', my: 1, py: 1, mx: 1, px: 2 }}>
              <Typography variant='body2' sx={{ marginRight: '1rem' }}>{data.sharedModel.title}</Typography>
            </DialogTitle>
            <DialogContent sx={{mb: 1}}>
              {previewImage && <img src={previewImage} alt="Preview" style={{ width: '100%' }} />}
            </DialogContent>
            <DialogActions sx={{ justifyContent: 'center', mb: 1, pb: 1, mx: 3 }}>
              {data.sharedModel.actions && data.sharedModel.actions.includes('Save') && (
                <Button startIcon={<SaveIcon />} onClick={onSave}>
                  保存
                </Button>
              )}
              {data.sharedModel.actions && data.sharedModel.actions.includes('Wechat') && (
                <Button startIcon={<Icon icon={'ic:baseline-wechat'} />} onClick={onShareToWeChat}>
                  微信
                </Button>
              )}
              {data.sharedModel.actions && data.sharedModel.actions.includes('More') && (
                <Button startIcon={<MoreHorizIcon />} onClick={onMoreOptions}>
                  更多
                </Button>
              )}
            </DialogActions>
          </Dialog>
        )}
      </Fragment>
    );
  };

  useEffect(() => {
    if (viewPageShareStatus) {
      handleGenerateShareImage()
    }
  }, [viewPageShareStatus]);

  console.log("viewPageShareStatus", viewPageShareStatus)

  if (data && data['单位名称']) {
    return (
      <Fragment>
        {viewPageShareStatus == true && (
          <Fragment>
            <ShareDialog
              previewImage={previewImage}
              onClose={() => {
                setPreviewImage(null)
                handSetViewPageShareStatus(false)
              }}
              onSave={handleDownloadImage}
              onShareToWeChat={handleShareToWeChat}
              onMoreOptions={handleShareImageToOther}
            />
          </Fragment>
        )}
        {loading && (
          <Backdrop sx={{ color: '#fff', zIndex: (theme) => theme.zIndex.drawer + 1 }} open={true} >
            <CircularProgress color="inherit" size={45}/>
          </Backdrop>
        )}
        <div ref={printRef}>
          <Card sx={{m: 0, p: 0}}>
            <CardContent sx={{p: 3, pl: 4}}>
              <Grid container>
                <Grid item sm={6} xs={12} sx={{ mb: { sm: 0, xs: 4 } }}>
                  <Box sx={{ display: 'flex', flexDirection: 'column' }}>
                    <Box sx={{ mb: 6, display: 'flex', alignItems: 'center' }}>
                      <Typography
                        variant='h6'
                        sx={{ fontWeight: 600, lineHeight: 'normal' }}
                      >
                        {data['单位名称']}
                      </Typography>
                    </Box>
                    <div>
                      {data['用户信息']['学号'] && data['用户信息']['学号'] != "" && (
                        <div style={{ display: 'flex', flexDirection: 'row', marginBottom: '0.5rem' }}>
                            <Typography variant='body2' sx={{ marginRight: '1rem' }}>学号:</Typography>
                            <Typography variant='body2' sx={{ marginRight: '1rem' }}>{data['用户信息']['学号']}</Typography>
                        </div>
                      )}
                      {data['用户信息']['用户名'] && data['用户信息']['用户名'] != "" && (
                        <div style={{ display: 'flex', flexDirection: 'row', marginBottom: '0.5rem' }}>
                            <Typography variant='body2' sx={{ marginRight: '1rem' }}>用户名:</Typography>
                            <Typography variant='body2' sx={{ marginRight: '1rem' }}>{data['用户信息']['用户名']}</Typography>
                        </div>
                      )}
                      {data['用户信息']['姓名'] && data['用户信息']['姓名'] != "" && (
                        <div style={{ display: 'flex', flexDirection: 'row', marginBottom: '0.5rem' }}>
                            <Typography variant='body2' sx={{ marginRight: '1rem' }}>姓名:</Typography>
                            <Typography variant='body2' sx={{ marginRight: '1rem' }}>{data['用户信息']['姓名']}</Typography>
                        </div>
                      )}
                      {data['用户信息']['班级'] && data['用户信息']['班级'] != "" && (
                        <div style={{ display: 'flex', flexDirection: 'row', marginBottom: '0.5rem' }}>
                            <Typography variant='body2' sx={{ marginRight: '1rem' }}>班级:</Typography>
                            <Typography variant='body2' sx={{ marginRight: '1rem' }}>{data['用户信息']['班级']}</Typography>
                        </div>
                      )}
                    </div>
                  </Box>
                </Grid>
                <Grid item sm={6} xs={12}>
                  <Box sx={{ display: 'flex', justifyContent: { xs: 'flex-start', sm: 'flex-end' } }}>
                    <Table sx={{ maxWidth: '400px' }}>
                      <TableBody>
                        <TableRow>
                          <MUITableCell colSpan={2}>
                            <Typography variant='h6'>{data['测评名称']}</Typography>
                          </MUITableCell>
                        </TableRow>
                        <TableRow>
                          <MUITableCell>
                            <Typography variant='body2'>测评时间:</Typography>
                          </MUITableCell>
                          <MUITableCell>
                            <Typography variant='body2' >
                            {data['用户信息']['测评时间']}
                            </Typography>
                          </MUITableCell>
                        </TableRow>
                        <TableRow>
                          <MUITableCell>
                            <Typography variant='body2'>测评用时:</Typography>
                          </MUITableCell>
                          <MUITableCell>
                            <Typography variant='body2' >
                            {data['用户信息']['使用时间']}
                            </Typography>
                          </MUITableCell>
                        </TableRow>
                        <TableRow>
                          <MUITableCell>
                            <Typography variant='body2'>咨询师:</Typography>
                          </MUITableCell>
                          <MUITableCell>
                            <Typography variant='body2' >
                            {data['用户信息']['咨询师'] ?? '无'}
                            </Typography>
                          </MUITableCell>
                        </TableRow>
                        {data['DeepSeek'] == "" && data['用户信息']['测评时间'] == null && (
                          <TableRow>
                            <MUITableCell>
                              <Typography variant='body2'>测评状态:</Typography>
                            </MUITableCell>
                            <MUITableCell>
                              <Typography variant='body2' sx={{ color: 'info.main', fontSize: '0.825rem' }}>
                                未开始测评
                              </Typography>
                            </MUITableCell>
                          </TableRow>
                        )}
                        {data['DeepSeek'] == "" && data['用户信息']['测评时间'] != null && (
                          <TableRow>
                            <MUITableCell>
                              <Typography variant='body2'>AI状态:</Typography>
                            </MUITableCell>
                            <MUITableCell>
                              <Typography variant='body2' sx={{ color: 'info.main', fontSize: '0.825rem' }}>
                                人工智能分析生成中,请耐心等待5分钟.
                              </Typography>
                            </MUITableCell>
                          </TableRow>
                        )}
                        {data['DeepSeek'] != "" && data['用户信息']['测评时间'] != null && (
                          <TableRow>
                            <MUITableCell>
                              <Typography variant='body2'>AI状态:</Typography>
                            </MUITableCell>
                            <MUITableCell>
                              <Typography variant='body2' sx={{ color: 'primary.main', fontSize: '0.825rem' }}>
                                人工智能分析完成.
                              </Typography>
                            </MUITableCell>
                          </TableRow>
                        )}
                      </TableBody>
                    </Table>
                  </Box>
                </Grid>
              </Grid>
            </CardContent>

            <Divider />

            <CardContent>
              <Grid container>
                <Grid item xs={12} sm={12} sx={{ mb: 0 }}>
                <Box sx={{ display: 'flex', flexDirection: 'column' }}>
                    <Box sx={{ mb: 4, display: 'flex', alignItems: 'center' }}>
                      <Typography variant='h6' sx={{ fontWeight: 400, lineHeight: 'normal' }} >
                        一、测评说明
                      </Typography>
                    </Box>
                    <Typography variant='body2' sx={{ marginRight: '1rem', whiteSpace: 'pre-line' }}>{data['测评说明']}</Typography>
                  </Box>
                </Grid>
              </Grid>
            </CardContent>

            <Divider />

            <CardContent>
              <Grid container>
                <Grid item xs={12} sm={12} sx={{ mb: 0 }}>
                <Box sx={{ display: 'flex', flexDirection: 'column' }}>
                    <Box sx={{ mb: 4, display: 'flex', alignItems: 'center' }}>
                      <Typography variant='h6' sx={{ fontWeight: 400, lineHeight: 'normal' }} >
                        二、评分标准
                      </Typography>
                    </Box>
                    <Typography variant='body2' sx={{ marginRight: '1rem', whiteSpace: 'pre-line' }}>{data['评分标准']}</Typography>
                  </Box>
                </Grid>
              </Grid>
            </CardContent>

            <Divider />

            <CardContent>
              <Grid container>
                <Grid item xs={12} sm={12} sx={{ mb: 0 }}>
                <Box sx={{ display: 'flex', flexDirection: 'column' }}>
                    <Box sx={{ mb: 4, display: 'flex', alignItems: 'center' }}>
                      <Typography variant='h6' sx={{ fontWeight: 400, lineHeight: 'normal' }} >
                        三、因子分析
                      </Typography>
                    </Box>

                    <Grid container spacing={2}>
                      <Grid item xs={12} md={8} sx={{ m: 0, p: 0 }}>
                        <Card>
                          <CardContent sx={{height: '315px'}}>
                            <ReactApexcharts ref={chartRef} type='radar' height={305} series={series} options={options} />
                          </CardContent>
                        </Card>
                      </Grid>
                      <Grid item xs={12} md={4} sx={{ m: 0, p: 0 }}>
                        <Card>
                          <CardContent sx={{height: '315px'}}>
                            <Box sx={{ display: 'flex', flexDirection: 'column' }}>
                                {data && data['测评分析'] && Object.keys(data['测评分析']).map((item: any, index: number)=>{

                                  return (
                                    <div key={index} style={{ display: 'flex', flexDirection: 'row', marginBottom: '0.45rem' }}>
                                        <Typography variant='body2' sx={{ marginRight: '1rem', whiteSpace: 'nowrap' }}>{item}:</Typography>
                                        <Typography variant='body2' sx={{ marginRight: '1rem' }}>{data['测评分析'][item]}</Typography>
                                    </div>
                                  )
                                })}
                            </Box>
                          </CardContent>
                        </Card>
                      </Grid>
                    </Grid>


                    {data && data['因子分析'] && data['因子分析'].length > 0 && data['因子分析'].map((item: any, index: number)=>{

                      return (
                        <TableContainer key={index} sx={{mb: 2}}>
                          <Table>
                            <TableHead>
                              <TableRow>
                                <TableCell colSpan={4} sx={{m: 0, pb: 1}}>
                                  <Box sx={{ display: 'flex', flexDirection: 'column' }}>
                                    <div>
                                      <div style={{ display: 'flex', flexDirection: 'row', marginBottom: '0.3rem' }}>
                                          <Typography sx={{ marginRight: '1rem', whiteSpace: 'nowrap' }}>{index+1} 因子名称:</Typography>
                                          <Typography sx={{ marginRight: '1rem' }}>{item['名称']}</Typography>
                                      </div>
                                      <div style={{ display: 'flex', flexDirection: 'row', marginBottom: '0.3rem' }}>
                                          <Typography variant='body2' sx={{ marginRight: '1rem', whiteSpace: 'nowrap' }}>因子分数:</Typography>
                                          <Typography variant='body2' sx={{ marginRight: '1rem' }}>{item['测评分数']}</Typography>
                                      </div>
                                      <div style={{ display: 'flex', flexDirection: 'row', marginBottom: '0.3rem' }}>
                                          <Typography variant='body2' sx={{ marginRight: '1rem', whiteSpace: 'nowrap' }}>因子解释:</Typography>
                                          <Typography variant='body2' sx={{ marginRight: '1rem' }}>{item['因子解释']}</Typography>
                                      </div>
                                    </div>
                                  </Box>
                                </TableCell>
                              </TableRow>
                            </TableHead>
                            <TableHead>
                              <TableRow>
                                <TableCell sx={{ py: 1, width: '70%' }} >项目</TableCell>
                                <TableCell sx={{ py: 1, width: '15%', whiteSpace: 'nowrap' }}>选项</TableCell>
                                <TableCell sx={{ py: 1, width: '15%', whiteSpace: 'nowrap' }}>分值</TableCell>
                              </TableRow>
                            </TableHead>
                            <TableBody>
                              {item && item['测评明细'] && item['测评明细'].length > 0 && item['测评明细'].map((Ceping: any, IndexCeping: number)=>{

                                return (
                                  <TableRow key={IndexCeping} sx={{ py: 1 }}>
                                    <TableCell sx={{m: 0, p: 1, width: '70%'}}>{Ceping['测评项目']}</TableCell>
                                    <TableCell sx={{m: 0, p: 1, width: '15%', whiteSpace: 'nowrap'}}>{Ceping['测评选项']}</TableCell>
                                    <TableCell sx={{m: 0, p: 1, width: '15%', whiteSpace: 'nowrap'}}>{Ceping['测评分值']}</TableCell>
                                  </TableRow>
                                )
                              })}
                            </TableBody>
                          </Table>
                        </TableContainer>
                      )
                    })}
                  </Box>
                </Grid>
              </Grid>
            </CardContent>

            <Divider />

            <CardContent>
              <Grid container>
                <Grid item xs={12} sm={12} sx={{ mb: 0 }}>
                <Box sx={{ display: 'flex', flexDirection: 'column' }}>
                    <Box sx={{ mb: 4, display: 'flex', alignItems: 'center' }}>
                      <Typography variant='h6' sx={{ fontWeight: 400, lineHeight: 'normal' }} >
                        四、人工智能分析(DeepSeek模型)
                      </Typography>
                    </Box>

                    <Grid container spacing={2}>
                      <Grid item xs={12} sx={{ m: 0, p: 0 }}>
                        <Card>
                          <CardContent>
                            <Typography sx={{ color: 'action.active', fontSize: '0.825rem' }}>
                                <ReactMarkdown>{data['DeepSeek'].replace('\n', '  \n')}</ReactMarkdown>
                            </Typography>
                            {data['DeepSeek'] == "" && data['用户信息']['测评时间'] != null && (
                              <Typography sx={{ color: 'info.main', fontSize: '0.825rem' }}>
                                人工智能分析生成中,请耐心等待5分钟.
                              </Typography>
                            )}
                          </CardContent>
                        </Card>
                      </Grid>
                    </Grid>

                  </Box>
                </Grid>
              </Grid>
            </CardContent>

            <Divider />

            <CardContent>
              <Typography variant='body2'>
                <strong>说明：</strong> 此报告前三部分由系统自动生成，第四部分到结尾部分由国产AI大模型生成，报告整体用于参考作用，其并不具备任何法律效力。如果需要具有法律效力的报告，请到国家认可的正规机构，并且在具有相关资格从业医师的协助下做出对应的心理测评工作。
              </Typography>
              <Typography variant='body2'>
                <strong>隐私：</strong> 本系统非常注重用户隐私，不会随意搜集、滥用和外泄用户信息。系统提交给国产大模型的数据，只涉及了测评数据，没有提交用户任何身份信息或是隐私信息给到大模型，请放心使用。
              </Typography>
              {printModel && printModel == "print" && isMobileData == false && (
                <Grid container justifyContent="flex-end">
                  <Button
                    target='_blank'
                    sx={{ mb: 3.5 }}
                    component={Link}
                    color='primary'
                    size="small"
                    variant='outlined'
                    href={`/print/middleschool/${btoa(`${id}____${backEndApi.replace('.php', '').replace('apps/apps_', '')}`)}`}
                  >
                    {'打印'}
                  </Button>
                </Grid>
              )}
            </CardContent>

          </Card>
        </div>
      </Fragment>
    )
  } else {
    return null
  }
}

export default ModelMiddleSchoolSoulAssessment
