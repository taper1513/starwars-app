import { BrowserRouter, Routes, Route } from 'react-router-dom';
import HomePage from './pages/Home';
import MovieDetailsPage from './pages/MovieDetailsPage';
import PersonDetailsPage from './pages/PersonDetailsPage';
import LayoutContainer from './layouts/LayoutContainer';
import ErrorBoundary from './components/ErrorBoundary';

function App() {
  return (
    <ErrorBoundary>
      <BrowserRouter>
        <LayoutContainer>
          <Routes>
            <Route path="/" element={<HomePage />} />
            <Route path="/movies/:id" element={<MovieDetailsPage />} />
            <Route path="/people/:id" element={<PersonDetailsPage />} />
          </Routes>
        </LayoutContainer>
      </BrowserRouter>
    </ErrorBoundary>
  );
}

export default App;
