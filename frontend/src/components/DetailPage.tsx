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
    <div className="bg-white shadow p-8 w-full max-w-2xl mx-auto mt-10 rounded">
      <h2 className="text-xl font-bold mb-2">{title}</h2>
      <div className="flex flex-col md:flex-row gap-8">
        <div className="flex-1">
          {subtitles.map((item) => (
            <div key={item.label} className="mb-4">
              <div className="font-semibold">{item.label}</div>
              <div className="text-gray-700 text-sm">{item.content}</div>
            </div>
          ))}
        </div>
        <div className="flex-1">
          <div className="font-semibold mb-2">{sideTitle}</div>
          <ul className="text-sm flex flex-col gap-1">
            {sideLinks.map((link) => (
              <li key={link.id}>
                <button
                  className="text-blue-500 hover:underline"
                  onClick={link.onClick}
                  type="button"
                >
                  {link.label}
                </button>
              </li>
            ))}
          </ul>
        </div>
      </div>
      <div className="mt-8 flex justify-start">
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