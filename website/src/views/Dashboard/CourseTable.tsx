'use client'

// React Imports
import { useState, useEffect, useMemo } from 'react'

// Next Imports
import Link from 'next/link'

// MUI Imports
import Card from '@mui/material/Card'
import CardHeader from '@mui/material/CardHeader'
import Checkbox from '@mui/material/Checkbox'
import LinearProgress from '@mui/material/LinearProgress'
import TablePagination from '@mui/material/TablePagination'
import TextField from '@mui/material/TextField'
import Typography from '@mui/material/Typography'
import type { TextFieldProps } from '@mui/material/TextField'

// Third-party Imports
import classnames from 'classnames'
import { rankItem } from '@tanstack/match-sorter-utils'
import {
  createColumnHelper,
  flexRender,
  getCoreRowModel,
  useReactTable,
  getFilteredRowModel,
  getFacetedRowModel,
  getFacetedUniqueValues,
  getFacetedMinMaxValues,
  getPaginationRowModel,
  getSortedRowModel
} from '@tanstack/react-table'
import type { ColumnDef, FilterFn } from '@tanstack/react-table'
import type { RankingInfo } from '@tanstack/match-sorter-utils'

// Type Imports
import type { Course } from '@/types/apps/academyTypes'

// Components Imports
import CustomAvatar from '@core/components/mui/Avatar'

// Util Imports

// Style Imports
import tableStyles from '@core/styles/table.module.css'

declare module '@tanstack/table-core' {
  interface FilterFns {
    fuzzy: FilterFn<unknown>
  }
  interface FilterMeta {
    itemRank: RankingInfo
  }
}

type CourseWithProgress = Course & {
  progressValue?: string
}

const fuzzyFilter: FilterFn<any> = (row, columnId, value, addMeta) => {
  // Rank the item
  const itemRank = rankItem(row.getValue(columnId), value)

  // Store the itemRank info
  addMeta({
    itemRank
  })

  // Return if the item should be filtered in/out
  return itemRank.passed
}

const DebouncedInput = ({
  value: initialValue,
  onChange,
  debounce = 500,
  ...props
}: {
  value: string | number
  onChange: (value: string | number) => void
  debounce?: number
} & Omit<TextFieldProps, 'onChange'>) => {
  // States
  const [value, setValue] = useState(initialValue)

  useEffect(() => {
    setValue(initialValue)
  }, [initialValue])

  useEffect(() => {
    const timeout = setTimeout(() => {
      onChange(value)
    }, debounce)

    return () => clearTimeout(timeout)
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [value])

  return <TextField {...props} value={value} onChange={e => setValue(e.target.value)} size='small' />
}

// Column Definitions
const columnHelper = createColumnHelper<CourseWithProgress>()

const CourseTable = ({ courseData }: { courseData?: Course[] }) => {
  // States
  const [rowSelection, setRowSelection] = useState({})
  // eslint-disable-next-line @typescript-eslint/no-unused-vars
  const [data, setData] = useState(...[courseData])
  const [globalFilter, setGlobalFilter] = useState('')

  const columns = useMemo<ColumnDef<CourseWithProgress, any>[]>(
    () => [
      {
        id: 'select',
        header: ({ table }) => (
          <Checkbox
            {...{
              checked: table.getIsAllRowsSelected(),
              indeterminate: table.getIsSomeRowsSelected(),
              onChange: table.getToggleAllRowsSelectedHandler()
            }}
          />
        ),
        cell: ({ row }) => (
          <Checkbox
            {...{
              checked: row.getIsSelected(),
              disabled: !row.getCanSelect(),
              indeterminate: row.getIsSomeSelected(),
              onChange: row.getToggleSelectedHandler()
            }}
          />
        )
      },
      columnHelper.accessor('courseTitle', {
        header: 'Course Name',
        cell: ({ row }) => (
          <div className='flex items-center gap-4'>
            <CustomAvatar variant='rounded' skin='light' color={row.original.color}>
              <i className={classnames('text-[28px]', row.original.logo)} />
            </CustomAvatar>
            <div className='flex flex-col gap-0.5'>
              <Typography
                component={Link}
                href={'/apps/academy/course-details'}
                className='font-medium hover:text-primary'
                color='text.primary'
              >
                {row.original.courseTitle}
              </Typography>
              <div className='flex items-center gap-2'>
                <CustomAvatar src={row.original.image} size={22} />
                <Typography variant='body2'>{row.original.user}</Typography>
              </div>
            </div>
          </div>
        )
      }),
      columnHelper.accessor('time', {
        header: 'Time',
        cell: ({ row }) => (
          <Typography className='font-medium' color='text.primary'>
            {row.original.time}
          </Typography>
        ),
        enableSorting: false
      }),
      columnHelper.accessor('progressValue', {
        header: 'progress',
        sortingFn: (rowA, rowB) => {
          if (
            !Math.floor((rowA.original.completedTasks / rowA.original.totalTasks) * 100) ||
            !Math.floor((rowB.original.completedTasks / rowB.original.totalTasks) * 100)
          )
            return 0

          return (
            Number(Math.floor((rowA.original.completedTasks / rowA.original.totalTasks) * 100)) -
            Number(Math.floor((rowB.original.completedTasks / rowB.original.totalTasks) * 100))
          )
        },
        cell: ({ row }) => (
          <div className='flex items-center gap-4 min-is-48'>
            <Typography
              className='font-medium'
              color='text.primary'
            >{`${Math.floor((row.original.completedTasks / row.original.totalTasks) * 100)}%`}</Typography>
            <LinearProgress
              color='primary'
              value={Math.floor((row.original.completedTasks / row.original.totalTasks) * 100)}
              variant='determinate'
              className='is-full bs-2'
            />
            <Typography variant='body2'>{`${row.original.completedTasks}/${row.original.totalTasks}`}</Typography>
          </div>
        )
      }),
      columnHelper.accessor('userCount', {
        header: 'Status',
        cell: ({ row }) => (
          <div className='flex items-center justify-between gap-5'>
            <div className='flex items-center gap-1.5'>
              <i className='ri-group-line text-primary' />
              <Typography>{row.original.userCount}</Typography>
            </div>
            <div className='flex items-center gap-1.5'>
              <i className='ri-computer-line text-info' />
              <Typography>{row.original.note}</Typography>
            </div>
            <div className='flex items-center gap-1.5'>
              <i className='ri-video-upload-line text-error' />
              <Typography>{row.original.view}</Typography>
            </div>
          </div>
        ),
        enableSorting: false
      })
    ],
    // eslint-disable-next-line react-hooks/exhaustive-deps
    []
  )

  const table = useReactTable({
    data: data as Course[],
    columns,
    filterFns: {
      fuzzy: fuzzyFilter
    },
    state: {
      rowSelection,
      globalFilter
    },
    initialState: {
      pagination: {
        pageSize: 5
      }
    },
    enableRowSelection: true, //enable row selection for all rows
    // enableRowSelection: row => row.original.age > 18, // or enable row selection conditionally per row
    globalFilterFn: fuzzyFilter,
    onRowSelectionChange: setRowSelection,
    getCoreRowModel: getCoreRowModel(),
    onGlobalFilterChange: setGlobalFilter,
    getFilteredRowModel: getFilteredRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    getFacetedRowModel: getFacetedRowModel(),
    getFacetedUniqueValues: getFacetedUniqueValues(),
    getFacetedMinMaxValues: getFacetedMinMaxValues()
  })

  return (
    <Card>
      <CardHeader
        title='Course you are taking'
        action={
          <DebouncedInput
            value={globalFilter ?? ''}
            onChange={value => setGlobalFilter(String(value))}
            placeholder='Search Course'
          />
        }
        className='flex-wrap gap-4'
      />
      <div className='overflow-x-auto'>
        <table className={tableStyles.table}>
          <thead>
            {table.getHeaderGroups().map(headerGroup => (
              <tr key={headerGroup.id}>
                {headerGroup.headers.map(header => (
                  <th key={header.id}>
                    {header.isPlaceholder ? null : (
                      <>
                        <div
                          className={classnames({
                            'flex items-center': header.column.getIsSorted(),
                            'cursor-pointer select-none': header.column.getCanSort()
                          })}
                          onClick={header.column.getToggleSortingHandler()}
                        >
                          {flexRender(header.column.columnDef.header, header.getContext())}
                          {{
                            asc: <i className='ri-arrow-up-s-line text-xl' />,
                            desc: <i className='ri-arrow-down-s-line text-xl' />
                          }[header.column.getIsSorted() as 'asc' | 'desc'] ?? null}
                        </div>
                      </>
                    )}
                  </th>
                ))}
              </tr>
            ))}
          </thead>
          {table.getFilteredRowModel().rows.length === 0 ? (
            <tbody>
              <tr>
                <td colSpan={table.getVisibleFlatColumns().length} className='text-center'>
                  No data available
                </td>
              </tr>
            </tbody>
          ) : (
            <tbody>
              {table
                .getRowModel()
                .rows.slice(0, table.getState().pagination.pageSize)
                .map(row => {
                  return (
                    <tr key={row.id} className={classnames({ selected: row.getIsSelected() })}>
                      {row.getVisibleCells().map(cell => (
                        <td key={cell.id}>{flexRender(cell.column.columnDef.cell, cell.getContext())}</td>
                      ))}
                    </tr>
                  )
                })}
            </tbody>
          )}
        </table>
      </div>
      <TablePagination
        rowsPerPageOptions={[5, 10, 25]}
        component='div'
        className='border-bs'
        count={table.getFilteredRowModel().rows.length}
        rowsPerPage={table.getState().pagination.pageSize}
        page={table.getState().pagination.pageIndex}
        onPageChange={(_, page) => {
          table.setPageIndex(page)
        }}
        onRowsPerPageChange={e => table.setPageSize(Number(e.target.value))}
      />
    </Card>
  )
}

export default CourseTable
