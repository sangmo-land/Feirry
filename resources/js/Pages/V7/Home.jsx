import { Abroad, Advice, Commitments, Contact, CoupDePouce, Difference, Expertises, Facts, Hero, Method, Offers, Projects } from '@/v7/sections';

/** Home: the 11 sections of the V7 mock-up, in order (CDC §03). */
export default function Home({ expertises, projects, offers, posts, campaign }) {
    return (
        <>
            <Hero />
            <Facts />
            <Difference />
            <Expertises expertises={expertises} />
            <Projects projects={projects} />
            <Method />
            <Commitments />
            <Offers offers={offers} />
            <CoupDePouce campaign={campaign} />
            <Abroad />
            <Advice posts={posts} />
            <Contact offers={offers} />
        </>
    );
}
