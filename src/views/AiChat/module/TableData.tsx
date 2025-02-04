// ** React Imports
import { useState, ChangeEvent } from 'react'

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

const TableData = ({ data }: any) => {
  const dataJson = JSON.parse(data)
  const dataJsonData = dataJson.data
  console.log("dataJsonData", dataJsonData)

  const columns: any[] = []
  if(dataJsonData && dataJsonData.length > 0) {
    const Header = dataJsonData[0]
    Object.keys(Header).map((Item: string)=>{
      columns.push({ id: Item, label: Item, align: 'center' });
    })
  }

  const [page, setPage] = useState<number>(0)
  const [rowsPerPage, setRowsPerPage] = useState<number>(10)

  const handleChangePage = (event: unknown, newPage: number) => {
    setPage(newPage)
  }

  const handleChangeRowsPerPage = (event: ChangeEvent<HTMLInputElement>) => {
    setRowsPerPage(+event.target.value)
    setPage(0)
  }

  return (
    <>
      <TableContainer component={Paper} sx={{ height: (dataJsonData && dataJsonData.length > 10 ? 380 : '100%') }}>
        <Table stickyHeader size='small'>
          <TableHead>
            <TableRow>
              {columns.map(column => (
                <TableCell key={column.id} align={column.align} sx={{ minWidth: column.minWidth }}>
                  {column.label}
                </TableCell>
              ))}
            </TableRow>
          </TableHead>
          <TableBody>
            {dataJsonData.slice(page * rowsPerPage, page * rowsPerPage + rowsPerPage).map((row: any) => {
              return (
                <TableRow hover tabIndex={-1} key={row.code}>
                  {columns.map((column: any) => {

                    return (
                      <TableCell key={column.id} align={column.align}>
                        {row[column.id]}
                      </TableCell>
                    )
                  })}
                </TableRow>
              )
            })}
          </TableBody>
        </Table>
      </TableContainer>
      {dataJsonData && dataJsonData.length > 10 && (
        <TablePagination
          rowsPerPageOptions={[10, 25, 50]}
          component='div'
          count={dataJsonData.length}
          rowsPerPage={rowsPerPage}
          page={page}
          onPageChange={handleChangePage}
          onRowsPerPageChange={handleChangeRowsPerPage}
        />
      )}
      {dataJsonData && dataJsonData.length == 0 && dataJson.message && (
        <Typography sx={{
          width: 'fit-content',
          fontSize: '0.875rem',
          p: theme => theme.spacing(0.5, 2, 0.5, 2),
          ml: 1,
          color: 'text.primary',
        }}
        >{dataJson.message}</Typography>
      )}
    </>
  )
}

export default TableData
