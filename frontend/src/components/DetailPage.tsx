import React from 'react';

type LinkItem = {
  id: string;
  label: string;
  onClick: () => void;
};

type DetailPageProps = {
  title: string;
  subtitles: { label: string; content: React.ReactNode }[];
  sideTitle: string;
  sideLinks: LinkItem[];
  onBack: () => void;
};

export default function DetailPage({
  title,
  subtitles,
  sideTitle,
  sideLinks,
  onBack,
}: DetailPageProps) {
  return (
    <div className="bg-white shadow p-8 w-[804px] mx-auto mt-10 ">
      <h2 className="text-xl font-bold mb-4">{title}</h2>
      <div className="flex flex-col md:flex-row gap-8">
        <div className="flex-1">
          {subtitles.map((item) => (
            <div key={item.label} className="mb-4">
              <div className="font-semibold mb-4">Details</div>
              <hr className="border-t w-[322px] border-gray-300 mb-4" />
              <div className="text-gray-700 text-sm">{item.content}</div>
            </div>
          ))}
        </div>
        <div className="flex-1">
          <div className="font-semibold mb-4 text-base]">{sideTitle}</div>
          <hr className="border-t w-[322px] border-gray-300 mb-4" />
          <div className="text-sm">
            {sideLinks.map((link, idx) => (
              <span key={link.id}>
                <button
                  className="text-blue-500 hover:underline"
                  onClick={link.onClick}
                  type="button"
                >
                  {link.label}
                </button>
                {idx < sideLinks.length - 1 && ', '}
              </span>
            ))}
          </div>
        </div>
      </div>
      <div className="mt-4 flex justify-start">
        <button
          onClick={onBack}
          className="rounded bg-green-500 text-white font-bold py-2 px-6 hover:bg-green-600 transition-all duration-200 shadow"
        >
          BACK TO SEARCH
        </button>
      </div>
    </div>
  );
} 