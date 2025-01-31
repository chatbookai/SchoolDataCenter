// ** React Imports
import { useState, useEffect } from 'react'

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
import OptionsMenu from 'src/@core/components/option-menu'
import CustomAvatar from 'src/@core/components/mui/avatar'

interface DataType2 {
  stats: string
  title: string
  color: ThemeColor
  icon: string
}

interface DataType {
  data: {[key:string]:any}
  handleOptionsMenuItemClick: (Item: string) => void
}

const AnalyticsTransactionsCard = (props: DataType) => {
  
  const { data, handleOptionsMenuItemClick } = props
  const router = useRouter();
  console.log("router",router)
  const [selectedItem, setSelectedItem] = useState<string>("")

  useEffect(() => {
    data.TopRightOptions.map((item:{[key:string]:any})=>{
      if(item.selected) {
        setSelectedItem(item.name)
      }
    })
  }, [data.TopRightOptions])

  return (
    <Card>
      <CardHeader
        title={data.Title}
        action={
          <OptionsMenu
            options={
              data.TopRightOptions.map((item:{[key:string]:any})=>{
                return {
                  text: item.name,
                  menuItemProps: {
                    sx: { py: 2 },
                    selected: selectedItem === item.name,
                    onClick: () => {
                      handleOptionsMenuItemClick(item.name)
                      console.log(item)
                      setSelectedItem(item.name)
                    }
                  }
                }
              })}
            iconButtonProps={{ size: 'small', sx: { color: 'text.primary' } }}
          />
        }
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
              <Grid item xs={12} sm={2.4} key={index}>
                <Box key={index} sx={{ display: 'flex', alignItems: 'center' }}>
                  <CustomAvatar
                    variant='rounded'
                    color={item.color}
                    sx={{ mr: 3, boxShadow: 3, width: 44, height: 44, '& svg': { fontSize: '1.75rem' } }}
                  >
                  <Icon icon={item.icon} />
                  </CustomAvatar>
                  <Box sx={{ display: 'flex', flexDirection: 'column' }}>
                    <Typography>{item.title}</Typography>
                    <Typography variant='h6'>{item.stats}</Typography>
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

export default AnalyticsTransactionsCard
