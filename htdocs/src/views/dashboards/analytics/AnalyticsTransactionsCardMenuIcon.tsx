// ** React Imports

// ** MUI Imports
import Box from '@mui/material/Box'
import Grid from '@mui/material/Grid'
import Card from '@mui/material/Card'
import CardHeader from '@mui/material/CardHeader'
import Typography from '@mui/material/Typography'
import CardContent from '@mui/material/CardContent'
import { useRouter } from 'next/router'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// ** Types
import { ThemeColor } from 'src/@core/layouts/types'

// ** Custom Components Imports
import CustomAvatar from 'src/@core/components/mui/avatar'

interface DataType2 {
  url: string
  title: string
  color: ThemeColor
  icon: string
}

interface DataType {
  data: {[key:string]:any}
  handleOptionsMenuItemClick: (Item: string) => void
}

const AnalyticsTransactionsCardMenuIcon = (props: DataType) => {
  
  const { data } = props
  const router = useRouter();
  console.log("router",router)

  return (
    <Card>
      <CardHeader
        title={data.Title}
        subheader={
          <Typography variant='body2'>
            <Box component='span' sx={{ fontWeight: 600, color: 'text.primary' }}>
            {data.SubTitle}
            </Box>{' '}
          </Typography>
        }
        titleTypographyProps={{
          sx: {
            mb: 2.5,
            lineHeight: '2rem !important',
            letterSpacing: '0.15px !important'
          }
        }}
      />
      <CardContent sx={{ pt: (theme: any) => `${theme.spacing(3)} !important` }}>
        <Grid container spacing={[5, 0]}>
          {
            data.data.map((item: DataType2, index: number) => (
              <Grid item xs={12} sm={2} key={index}>
                <Box key={index} sx={{ display: 'flex', alignItems: 'center', mb: 3 }} onClick={() => router.push(item.url)}>
                  <CustomAvatar
                      variant='rounded'
                      color={item.color}
                      sx={{ mr: 3, boxShadow: 3, width: 44, height: 44, '& svg': { fontSize: '1.75rem' } }}
                  >
                  <Icon icon={item.icon} />
                  </CustomAvatar>
                  <Box sx={{ display: 'flex', flexDirection: 'column' }}>
                    <Typography>{item.title}</Typography>
                  </Box>
                </Box>
              </Grid>
            ))
          }
        </Grid>
      </CardContent>
    </Card>
  )
}

export default AnalyticsTransactionsCardMenuIcon
