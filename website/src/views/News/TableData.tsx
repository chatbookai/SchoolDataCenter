// ** React Imports
import { useState, ChangeEvent, useEffect } from 'react'

// ** MUI Imports
import Paper from '@mui/material/Paper'
import Table from '@mui/material/Table'
import TableRow from '@mui/material/TableRow'
import TableHead from '@mui/material/TableHead'
import TableBody from '@mui/material/TableBody'
import TableCell from '@mui/material/TableCell'
import TableContainer from '@mui/material/TableContainer'
import TablePagination from '@mui/material/TablePagination'
import Typography from '@mui/material/Typography'

import axios from 'axios'
import authConfig from '@configs/auth'


const TableData = ({ type }: any) => {

  useEffect(() => {
    getNewsDataList()
  }, [])

  const [newsData, setNewsData] = useState<any>(null)

  const getNewsDataList = async () => {
      try {
          const RS = await axios.post(authConfig.backEndApiHost + 'website/list.php', { type }, {
          headers: {
              'Content-Type': 'application/json'
          }
          }).then(res=>res.data)
          setNewsData(RS)
      }
      catch(Error: any) {
          console.log("getChatLogList Error", Error)
      }
  }

  console.log("newsData", newsData)

  const [page, setPage] = useState<number>(0)
  const [rowsPerPage, setRowsPerPage] = useState<number>(5)

  const handleChangePage = (event: unknown, newPage: number) => {
    setPage(newPage)
  }

  const handleChangeRowsPerPage = (event: ChangeEvent<HTMLInputElement>) => {
    setRowsPerPage(+event.target.value)
    setPage(0)
  }

  return (
    <>
      <TableContainer component={Paper} sx={{ height: 380 }}>
        <Table stickyHeader size='small'>
          <TableHead>
            <TableRow>
              <TableCell align={'center'} sx={{ minWidth: '200px', width: '70%' }}>
                {'标题'}
              </TableCell>
              <TableCell align={'center'} sx={{ minWidth: '120px', width: '20%' }}>
                {'发布时间'}
              </TableCell>
              <TableCell align={'center'} sx={{ minWidth: '70px', width: '10%' }}>
                {'阅读'}
              </TableCell>
            </TableRow>
          </TableHead>
          <TableBody>
            {newsData && newsData.data.slice(page * rowsPerPage, page * rowsPerPage + rowsPerPage).map((row: any) => {
              return (
                <TableRow hover tabIndex={-1} key={row.code}>
                  <TableCell align={'left'}>
                    {row['标题']}
                  </TableCell>
                  <TableCell align={'center'}>
                    {row['创建时间'].slice(0, 10)}
                  </TableCell>
                  <TableCell align={'center'}>
                    {row['阅读次数']}
                  </TableCell>
                </TableRow>
              )
            })}
          </TableBody>
        </Table>
      </TableContainer>
      {newsData && newsData.data && newsData.data.length > 5 && (
        <TablePagination
          rowsPerPageOptions={[5, 10, 25, 50]}
          component='div'
          count={newsData.data.length}
          rowsPerPage={rowsPerPage}
          page={page}
          onPageChange={handleChangePage}
          onRowsPerPageChange={handleChangeRowsPerPage}
        />
      )}
    </>
  )
}

export default TableData
