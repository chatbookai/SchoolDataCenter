
// ** MUI Imports
import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import { styled } from '@mui/material/styles'
import CardHeader from '@mui/material/CardHeader'
import Typography from '@mui/material/Typography'
import CardContent from '@mui/material/CardContent'
import MuiDivider, { DividerProps } from '@mui/material/Divider'
import { useRouter } from 'next/router'

// ** Icon Imports
import Icon from 'src/@core/components/icon'
import CustomAvatar from 'src/@core/components/mui/avatar'

// ** Types
import { ThemeColor } from 'src/@core/layouts/types'

interface DataType2 {
  图标颜色: ThemeColor
  项目图标: string
  积分项目: string
  二级指标: string
  积分分值: number
}

// Styled Divider component
const Divider = styled(MuiDivider)<DividerProps>(({ theme }) => ({
  margin: `${theme.spacing(5, 0)} !important`,
  borderRight: `1px solid ${theme.palette.divider}`,
  [theme.breakpoints.down('md')]: {
    borderRight: 'none',
    margin: theme.spacing(0, 5),
    borderBottom: `1px solid ${theme.palette.divider}`
  }
}))

interface DataType {
  data: {[key:string]:any}
  handleOptionsMenuItemClick: (Item: string) => void
}

const AnalyticsDepositWithdraw = (props: DataType) => {
  
  const { data, handleOptionsMenuItemClick } = props
  const router = useRouter();
  console.log("handleOptionsMenuItemClick",handleOptionsMenuItemClick)

  return (
    <Card sx={{ display: 'flex', justifyContent: 'space-between', flexDirection: ['column', 'column', 'row'] }}>
      <Box sx={{ width: '100%' }}>
        <CardHeader
          title={data['加分']['Title']}
          sx={{ pt: 5.5, alignItems: 'center', '& .MuiCardHeader-action': { mt: 0.6 } }}
          action={<Typography variant='caption' onClick={() => router.push(data['加分']['TopRightButton']['url'])} style={{ cursor: 'pointer' }}>{data['加分']['TopRightButton']['name']}</Typography>}
          titleTypographyProps={{
            variant: 'h6',
            sx: { lineHeight: '1.6 !important', letterSpacing: '0.15px !important' }
          }}
        />
        <CardContent sx={{ pb: (theme: any) => `${theme.spacing(9)} !important` }}>
          {data['加分']['data'].map((item: DataType2, index: number) => {
            return (
              <Box
                key={index}
                sx={{ display: 'flex', alignItems: 'center', mb: index !== data['加分']['data'].length - 1 ? 6 : 0 }}
              >
                <Box sx={{ minWidth: 38, display: 'flex', justifyContent: 'center' }}>
                  <CustomAvatar
                    variant='rounded'
                    color={item.图标颜色}
                    sx={{ mr: 3, boxShadow: 3, width: 44, height: 44, '& svg': { fontSize: '1.75rem' } }}
                  >
                  <Icon icon={item.项目图标} />
                  </CustomAvatar>
                </Box>
                <Box
                  sx={{
                    ml: 4,
                    width: '100%',
                    display: 'flex',
                    flexWrap: 'wrap',
                    alignItems: 'center',
                    justifyContent: 'space-between'
                  }}
                >
                  <Box sx={{ mr: 2, display: 'flex', flexDirection: 'column' }}>
                    <Typography sx={{ 'fontWeight': 600, 'fontSize': '0.875rem', 'display': 'inline-block', 'width': '240px', 'whiteSpace': 'nowrap', 'overflow': 'hidden', 'textOverflow': 'ellipsis' }}>{item.积分项目}</Typography>
                    <Typography variant='caption'>{item.二级指标}</Typography>
                  </Box>
                  <Typography variant='subtitle2' sx={{ fontWeight: 600, color: 'success.main' }}>
                    {item.积分分值}
                  </Typography>
                </Box>
              </Box>
            )
          })}
        </CardContent>
      </Box>

      <Divider flexItem />

      <Box sx={{ width: '100%' }}>
        <CardHeader
          title={data['扣分']['Title']}
          sx={{ pt: 5.5, alignItems: 'center', '& .MuiCardHeader-action': { mt: 0.6 } }}
          action={<Typography variant='caption' onClick={() => router.push(data['扣分']['TopRightButton']['url'])} style={{ cursor: 'pointer' }}>{data['扣分']['TopRightButton']['name']}</Typography>}
          titleTypographyProps={{
            variant: 'h6',
            sx: { lineHeight: '1.6 !important', letterSpacing: '0.15px !important' }
          }}
        />
        <CardContent sx={{ pb: (theme: any) => `${theme.spacing(5.5)} !important` }}>
          {data['扣分']['data'].map((item: DataType2, index: number) => {
            return (
              <Box
                key={index}
                sx={{ display: 'flex', alignItems: 'center', mb: index !== data['扣分']['data'].length - 1 ? 6 : 0 }}
              >
                <Box sx={{ minWidth: 38, display: 'flex', justifyContent: 'center' }}>
                  <CustomAvatar
                    variant='rounded'
                    color={item.图标颜色}
                    sx={{ mr: 3, boxShadow: 3, width: 44, height: 44, '& svg': { fontSize: '1.75rem' } }}
                  >
                  <Icon icon={item.项目图标} />
                  </CustomAvatar>
                </Box>
                <Box
                  sx={{
                    ml: 4,
                    width: '100%',
                    display: 'flex',
                    flexWrap: 'wrap',
                    alignItems: 'center',
                    justifyContent: 'space-between'
                  }}
                >
                  <Box sx={{ mr: 2, display: 'flex', flexDirection: 'column' }}>
                    <Typography sx={{ 'fontWeight': 600, 'fontSize': '0.875rem', 'display': 'inline-block', 'width': '240px', 'whiteSpace': 'nowrap', 'overflow': 'hidden', 'textOverflow': 'ellipsis' }}>{item.积分项目}</Typography>
                    <Typography variant='caption'>{item.二级指标}</Typography>
                  </Box>
                  <Typography variant='subtitle2' sx={{ fontWeight: 600, color: 'error.main' }}>
                    {item.积分分值}
                  </Typography>
                </Box>
              </Box>
            )
          })}
        </CardContent>
      </Box>
    </Card>
  )
}

export default AnalyticsDepositWithdraw
