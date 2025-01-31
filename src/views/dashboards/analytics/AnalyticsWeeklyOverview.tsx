// ** React Imports
import { useState, useEffect } from 'react'

// ** MUI Imports
import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import Button from '@mui/material/Button'
import { useTheme } from '@mui/material/styles'
import CardHeader from '@mui/material/CardHeader'
import Typography from '@mui/material/Typography'
import CardContent from '@mui/material/CardContent'
import { useRouter } from 'next/router'

// ** Third Party Imports
import { ApexOptions } from 'apexcharts'

// ** Custom Components Imports
import OptionsMenu from 'src/@core/components/option-menu'
import ReactApexcharts from 'src/@core/components/react-apexcharts'

interface DataType {
  data: {[key:string]:any}
  handleOptionsMenuItemClick: (Item: string) => void
}

const AnalyticsWeeklyOverview = (props: DataType) => {
  
  const { data, handleOptionsMenuItemClick } = props
  const [selectedItem, setSelectedItem] = useState<string>("")

  useEffect(() => {
    data.TopRightOptions.map((item:{[key:string]:any})=>{
      if(item.selected) {
        setSelectedItem(item.name)
      }
    })
  }, [data.TopRightOptions])

  const router = useRouter();
  
  // ** Hook
  const theme = useTheme()

  const options: ApexOptions = {
    chart: {
      parentHeightOffset: 0,
      toolbar: { show: false }
    },
    plotOptions: {
      bar: {
        borderRadius: 9,
        distributed: true,
        columnWidth: '40%',
        endingShape: 'rounded',
        startingShape: 'rounded'
      }
    },
    stroke: {
      width: 2,
      colors: [theme.palette.background.paper]
    },
    legend: { show: false },
    grid: {
      strokeDashArray: 7,
      borderColor: theme.palette.divider,
      padding: {
        top: -1,
        left: -9,
        right: 0,
        bottom: 5
      }
    },
    dataLabels: { enabled: false },
    colors: [
      theme.palette.customColors.trackBg,
      theme.palette.customColors.trackBg,
      theme.palette.customColors.trackBg,
      theme.palette.primary.main,
      theme.palette.customColors.trackBg,
      theme.palette.customColors.trackBg
    ],
    states: {
      hover: {
        filter: { type: 'none' }
      },
      active: {
        filter: { type: 'none' }
      }
    },
    xaxis: {
      categories: data.dataX,
      tickPlacement: 'on',
      labels: { show: false },
      axisTicks: { show: false },
      axisBorder: { show: false }
    },
    yaxis: {
      show: true,
      tickAmount: 4,
      labels: {
        offsetY: 2,
        offsetX: -17,
        style: { colors: theme.palette.text.disabled }
      }
    }
  }

  return (
    <Card>
      <CardHeader
        title={data.Title}
        titleTypographyProps={{
          sx: { lineHeight: '2rem !important', letterSpacing: '0.15px !important' }
        }}
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
      <CardContent sx={{ '& .apexcharts-xcrosshairs.apexcharts-active': { opacity: 0 } }}>
        <ReactApexcharts type='bar' height={205} options={options} series={data.dataY} />
        <Box sx={{ mb: 7, display: 'flex', alignItems: 'center' }}>
          <Typography variant='h5' sx={{ mr: 4 }}>
            {data.BottomText.Left}
          </Typography>
          <Typography variant='body2'>{data.BottomText.Right}</Typography>
        </Box>
        <Button fullWidth variant='contained' onClick={() => router.push(data.ViewButton.url)}>
        {data.ViewButton.name}
        </Button>
      </CardContent>
    </Card>
  )
}

export default AnalyticsWeeklyOverview
