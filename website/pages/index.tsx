import type { NextPage } from "next"
import Head from "next/head"
import styles from "../styles/Home.module.css"

const Home: NextPage = () => {
	return (
		<div className={styles.container}>
			<Head>
				<title>Muqsit/CurlWithSSL Example Website</title>
				<meta name="description" content="A website providing example APIs for Muqsit/CurlWithSSL" />
				<link rel="icon" href="/favicon.ico" />
			</Head>

			<main className={styles.main}>
				<h1 className={styles.title}>
					<a href="https://github.com/Muqsit/CurlWithSSL">Muqsit/CurlWithSSL</a> Example Website
				</h1>

				<p className={styles.description}>
					This website provides the following example API endpoints:
				</p>

				<div className={styles.card}>
					<h2><code>GET api/profile</code></h2>
					<p>Returns 200 with body <code>{JSON.stringify({"name": "John Doe"})}</code></p>
				</div>
				<div className={styles.card}>
					<h2><code>POST api/profile</code></h2>
					<p>Returns 200 with body <code>{JSON.stringify({"message": "Successfully updated profile information."})}</code></p>
				</div>
			</main>
		</div>
	)
}

export default Home
