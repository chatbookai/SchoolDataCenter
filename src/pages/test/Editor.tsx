import React from 'react';
import { useEditor, EditorContent } from '@tiptap/react';
import StarterKit from '@tiptap/starter-kit';

const Editor = () => {
  const editor = useEditor({
    extensions: [
      StarterKit.configure({
        // Disable an included extension
        history: false,

        // Configure an included extension
        heading: {
          levels: [1, 2],
        },
      }),
    ],
    content: '<p>开始输入...</p>',
  });

  return <EditorContent editor={editor} />;
};

export default Editor;
