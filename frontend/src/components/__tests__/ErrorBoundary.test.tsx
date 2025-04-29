import { render, screen } from '@testing-library/react';
import ErrorBoundary from '../ErrorBoundary';

function ProblemChild() : JSX.Element {
  throw new Error('Crashed!');
}

describe('ErrorBoundary', () => {
  beforeEach(() => {
    jest.spyOn(console, 'error').mockImplementation(() => {});
  });

  afterEach(() => {
    (console.error as jest.Mock).mockRestore();
  });

  it('renders fallback UI on error', () => {
    render(
      <ErrorBoundary>
        <ProblemChild />
      </ErrorBoundary>
    );
    expect(screen.getByText(/something went wrong/i)).toBeInTheDocument();
    expect(screen.getByText(/crashed!/i)).toBeInTheDocument();
    expect(screen.getByText(/reload page/i)).toBeInTheDocument();
  });

  it('renders children when no error', () => {
    render(
      <ErrorBoundary>
        <div>Safe Child</div>
      </ErrorBoundary>
    );
    expect(screen.getByText(/safe child/i)).toBeInTheDocument();
  });
}); 