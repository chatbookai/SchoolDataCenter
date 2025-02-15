declare module 'react-excel-renderer';
declare module 'react-file-viewer';
declare module 'react-json-viewer';
declare module '*.module.css';
declare module '@emotion/react';
declare module 'secrets.js';

declare global {
    interface Window {
        Arweave: typeof Arweave;
    }
    module globalThis {
    }
}

export {};