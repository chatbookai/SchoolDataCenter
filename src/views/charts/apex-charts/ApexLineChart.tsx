// ** React Imports
import { useState, useEffect } from 'react'

// ** MUI Imports
import Card from '@mui/material/Card'
import { useTheme } from '@mui/material/styles'
import CardHeader from '@mui/material/CardHeader'
import CardContent from '@mui/material/CardContent'

// ** Third Party Imports
import { ApexOptions } from 'apexcharts'

// ** Custom Components Imports
import ReactApexcharts from 'src/@core/components/react-apexcharts'

// ** Custom Components Imports
import OptionsMenu from 'src/@core/components/option-menu'


interface DataType {
  data: {[key:string]:any}
  handleOptionsMenuItemClick: (Item: string) => void
}

const ApexLineChart = (props: DataType) => {
  
  const { data, handleOptionsMenuItemClick } = props
  const [selectedItem, setSelectedItem] = useState<string>("")

  useEffect(() => {
    data.TopRightOptions.map((item:{[key:string]:any})=>{
      if(item.selected) {
        setSelectedItem(item.name)
      }
    })
  }, [data.TopRightOptions])

  // ** Hook
  const theme = useTheme()

  const options: ApexOptions = {
    chart: {
      parentHeightOffset: 0,
      zoom: { enabled: false },
      toolbar: { show: false }
    },
    colors: ['#ff9f43'],
    stroke: { curve: 'straight' },
    dataLabels: { enabled: false },
    markers: {
      strokeWidth: 7,
      strokeOpacity: 1,
      colors: ['#ff9f43'],
      strokeColors: ['#fff']
    },
    grid: {
      padding: { top: -10 },
      borderColor: theme.palette.divider,
      xaxis: {
        lines: { show: true }
      }
    },
    tooltip: {
      custom(data: any) {
        return `<div class='bar-chart'>
          <span>${data.series[data.seriesIndex][data.dataPointIndex]}</span>
        </div>`
      }
    },
    yaxis: {
      labels: {
        style: { colors: theme.palette.text.disabled }
      }
    },
    xaxis: {
      axisBorder: { show: false },
      axisTicks: { color: theme.palette.divider },
      crosshairs: {
        stroke: { color: theme.palette.divider }
      },
      labels: {
        style: { colors: theme.palette.text.disabled }
      },
      categories: data.dataX
    }
  }

  return (
    <Card>
      <CardHeader
        title={data.Title}
        subheader={data.SubTitle}
        sx={{
          flexDirection: ['column', 'row'],
          alignItems: ['flex-start', 'center'],
          '& .MuiCardHeader-action': { mb: 0 },
          '& .MuiCardHeader-content': { mb: [2, 0] }
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
      <CardContent>
        <ReactApexcharts type='line' height={283} options={options} series={data.dataY} />
      </CardContent>
    </Card>
  )
}

export default ApexLineChart
