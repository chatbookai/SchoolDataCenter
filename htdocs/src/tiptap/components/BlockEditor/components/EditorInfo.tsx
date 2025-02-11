import { memo } from 'react'

export type EditorInfoProps = {
  characters: number
  words: number
  saveEditorContent: () => void
}

export const EditorInfo = memo(({ saveEditorContent, characters, words }: EditorInfoProps) => {
  return (
    <div className="flex items-center">
      <div className="flex flex-col justify-center pr-4 mr-4 text-right border-r border-neutral-200 dark:border-neutral-800">
        <div className="text-xs font-semibold text-neutral-500 dark:text-neutral-400">
          {words} {words === 1 ? 'word' : 'words'}
        </div>
        <div className="text-xs font-semibold text-neutral-500 dark:text-neutral-400">
          {characters} {characters === 1 ? 'character' : 'characters'}
        </div>
      </div>
      <div
        className="flex items-center gap-2 mr-2 cursor-pointer active:scale-95 transition-transform"
        onClick={() => saveEditorContent()}
      >
        <span className="max-w-[4rem] text-xs text-neutral-500 dark:text-neutral-400 font-semibold">
          保存
        </span>
      </div>

    </div>
  )
})

EditorInfo.displayName = 'EditorInfo'
