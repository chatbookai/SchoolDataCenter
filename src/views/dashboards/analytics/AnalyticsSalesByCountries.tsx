// ** React Imports
import { useState, useEffect } from 'react'

// ** MUI Imports
import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import Typography from '@mui/material/Typography'
import CardHeader from '@mui/material/CardHeader'
import CardContent from '@mui/material/CardContent'

// ** Config
import authConfig from 'src/configs/auth'

// ** Types
import { ThemeColor } from 'src/@core/layouts/types'

// ** Custom Components Imports
import OptionsMenu from 'src/@core/components/option-menu'
import CustomAvatar from 'src/@core/components/mui/avatar'

interface DataType2 {
  学号: string
  姓名: string
  班级: string
  图标颜色: ThemeColor
  积分分值: string
  头像: string
}

interface DataType {
  data: {[key:string]:any}
  handleOptionsMenuItemClick: (Item: string) => void
}

const AnalyticsSalesByCountries = (props: DataType) => {
  
  const { data, handleOptionsMenuItemClick } = props
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
        titleTypographyProps={{ sx: { lineHeight: '1.2 !important', letterSpacing: '0.31px !important' } }}
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
      />
      <CardContent sx={{ pt: (theme: any) => `${theme.spacing(0.5)} !important` }}>
        {data.data.map((item: DataType2, index: number) => {
          return (
            <Box
              key={index}
              sx={{
                display: 'flex',
                alignItems: 'center',
                ...(index !== data.length - 1 ? { mb: 6.25 } : {})
              }}
            >
              <CustomAvatar
                src={authConfig.backEndApiHost+item.头像}
                skin='light'
                color={item.图标颜色}
                sx={{ width: 38, height: 38, mr: 3, fontSize: '1rem' }}
              >
              {item.姓名.substring(0,1)}
              </CustomAvatar>
              <Box
                sx={{
                  width: '100%',
                  display: 'flex',
                  flexWrap: 'wrap',
                  alignItems: 'center',
                  justifyContent: 'space-between'
                }}
              >
                <Box sx={{ mr: 2, display: 'flex', flexDirection: 'column' }}>
                  <Box sx={{ display: 'flex' }}>
                    <Typography sx={{ mr: 0.5, fontWeight: 600, letterSpacing: '0.25px' }}>{item.姓名}</Typography>
                  </Box>
                  <Typography variant='caption' sx={{ lineHeight: 1.5 }}>
                    {item.学号}
                  </Typography>
                </Box>

                <Box sx={{ display: 'flex', textAlign: 'end', flexDirection: 'column' }}>
                  <Typography sx={{ fontWeight: 600, fontSize: '0.875rem', lineHeight: 1.72, letterSpacing: '0.22px' }}>
                    {item.积分分值}
                  </Typography>
                  <Typography variant='caption' sx={{ lineHeight: 1.5 }}>
                  {index+1}
                  </Typography>
                </Box>
              </Box>
            </Box>
          )
        })}
      </CardContent>
    </Card>
  )
}

export default AnalyticsSalesByCountries
