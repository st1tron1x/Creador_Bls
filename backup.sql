--
-- PostgreSQL database dump
--

\restrict P66ZKlPHeJhIDEoL9HdYos23T9ti4p2DnXQaxn0PPop6iGGhi6za02hoqvyiR8p

-- Dumped from database version 16.15
-- Dumped by pg_dump version 16.15

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: bills_of_lading; Type: TABLE; Schema: public; Owner: bl_platform_user
--

CREATE TABLE public.bills_of_lading (
    id bigint NOT NULL,
    port_id bigint NOT NULL,
    sequence_number integer,
    bl_number character varying(20),
    status character varying(255) DEFAULT 'borrador'::character varying NOT NULL,
    country_origin character varying(255),
    shipper_ref character varying(255),
    shipper text,
    consignee text,
    notify_party text,
    pre_carriage_by character varying(255),
    place_of_receipt character varying(255),
    ocean_vessel_voy_no character varying(255),
    port_of_discharge character varying(255),
    place_of_delivery character varying(255),
    final_destination character varying(255),
    freight_payable_at character varying(255),
    number_of_original_bsl character varying(255),
    temperature_control_instructions text,
    freight_type character varying(255),
    freight_total numeric(12,2),
    freight_and_charges text,
    excess_value_declaration text,
    on_board_date date,
    delivery_agent character varying(255),
    created_by bigint NOT NULL,
    updated_by bigint,
    issued_at timestamp(0) without time zone,
    voided_by bigint,
    voided_at timestamp(0) without time zone,
    voided_reason text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    bill_of_loading_ref character varying(255),
    CONSTRAINT bills_of_lading_freight_type_check CHECK (((freight_type)::text = ANY (ARRAY[('prepaid'::character varying)::text, ('collect'::character varying)::text]))),
    CONSTRAINT bills_of_lading_status_check CHECK (((status)::text = ANY (ARRAY[('borrador'::character varying)::text, ('emitido'::character varying)::text, ('anulado'::character varying)::text, ('anulacion_solicitada'::character varying)::text])))
);


ALTER TABLE public.bills_of_lading OWNER TO bl_platform_user;

--
-- Name: bills_of_lading_id_seq; Type: SEQUENCE; Schema: public; Owner: bl_platform_user
--

CREATE SEQUENCE public.bills_of_lading_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.bills_of_lading_id_seq OWNER TO bl_platform_user;

--
-- Name: bills_of_lading_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: bl_platform_user
--

ALTER SEQUENCE public.bills_of_lading_id_seq OWNED BY public.bills_of_lading.id;


--
-- Name: bl_audit_logs; Type: TABLE; Schema: public; Owner: bl_platform_user
--

CREATE TABLE public.bl_audit_logs (
    id bigint NOT NULL,
    bill_of_lading_id bigint NOT NULL,
    user_id bigint,
    action character varying(40) NOT NULL,
    detail text,
    created_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.bl_audit_logs OWNER TO bl_platform_user;

--
-- Name: bl_audit_logs_id_seq; Type: SEQUENCE; Schema: public; Owner: bl_platform_user
--

CREATE SEQUENCE public.bl_audit_logs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.bl_audit_logs_id_seq OWNER TO bl_platform_user;

--
-- Name: bl_audit_logs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: bl_platform_user
--

ALTER SEQUENCE public.bl_audit_logs_id_seq OWNED BY public.bl_audit_logs.id;


--
-- Name: bl_cargo_items; Type: TABLE; Schema: public; Owner: bl_platform_user
--

CREATE TABLE public.bl_cargo_items (
    id bigint NOT NULL,
    bill_of_lading_id bigint NOT NULL,
    sort_order integer DEFAULT 0 NOT NULL,
    marks_and_numbers text,
    container_seal_no character varying(255),
    no_of_pkgs character varying(255),
    description_of_goods text,
    gross_weight character varying(255),
    measurement_cbm character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.bl_cargo_items OWNER TO bl_platform_user;

--
-- Name: bl_cargo_items_id_seq; Type: SEQUENCE; Schema: public; Owner: bl_platform_user
--

CREATE SEQUENCE public.bl_cargo_items_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.bl_cargo_items_id_seq OWNER TO bl_platform_user;

--
-- Name: bl_cargo_items_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: bl_platform_user
--

ALTER SEQUENCE public.bl_cargo_items_id_seq OWNED BY public.bl_cargo_items.id;


--
-- Name: bl_void_requests; Type: TABLE; Schema: public; Owner: bl_platform_user
--

CREATE TABLE public.bl_void_requests (
    id bigint NOT NULL,
    bill_of_lading_id bigint NOT NULL,
    requested_by bigint NOT NULL,
    requested_reason text NOT NULL,
    requested_at timestamp(0) without time zone NOT NULL,
    status character varying(255) DEFAULT 'pendiente'::character varying NOT NULL,
    resolved_by bigint,
    resolved_at timestamp(0) without time zone,
    resolution_note text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT bl_void_requests_status_check CHECK (((status)::text = ANY (ARRAY[('pendiente'::character varying)::text, ('aprobada'::character varying)::text, ('rechazada'::character varying)::text])))
);


ALTER TABLE public.bl_void_requests OWNER TO bl_platform_user;

--
-- Name: bl_void_requests_id_seq; Type: SEQUENCE; Schema: public; Owner: bl_platform_user
--

CREATE SEQUENCE public.bl_void_requests_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.bl_void_requests_id_seq OWNER TO bl_platform_user;

--
-- Name: bl_void_requests_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: bl_platform_user
--

ALTER SEQUENCE public.bl_void_requests_id_seq OWNED BY public.bl_void_requests.id;


--
-- Name: cache; Type: TABLE; Schema: public; Owner: bl_platform_user
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration bigint NOT NULL
);


ALTER TABLE public.cache OWNER TO bl_platform_user;

--
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: bl_platform_user
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration bigint NOT NULL
);


ALTER TABLE public.cache_locks OWNER TO bl_platform_user;

--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: bl_platform_user
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection character varying(255) NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.failed_jobs OWNER TO bl_platform_user;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: bl_platform_user
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.failed_jobs_id_seq OWNER TO bl_platform_user;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: bl_platform_user
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: job_batches; Type: TABLE; Schema: public; Owner: bl_platform_user
--

CREATE TABLE public.job_batches (
    id character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    total_jobs integer NOT NULL,
    pending_jobs integer NOT NULL,
    failed_jobs integer NOT NULL,
    failed_job_ids text NOT NULL,
    options text,
    cancelled_at integer,
    created_at integer NOT NULL,
    finished_at integer
);


ALTER TABLE public.job_batches OWNER TO bl_platform_user;

--
-- Name: jobs; Type: TABLE; Schema: public; Owner: bl_platform_user
--

CREATE TABLE public.jobs (
    id bigint NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    attempts smallint NOT NULL,
    reserved_at integer,
    available_at integer NOT NULL,
    created_at integer NOT NULL
);


ALTER TABLE public.jobs OWNER TO bl_platform_user;

--
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: bl_platform_user
--

CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.jobs_id_seq OWNER TO bl_platform_user;

--
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: bl_platform_user
--

ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: bl_platform_user
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO bl_platform_user;

--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: bl_platform_user
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.migrations_id_seq OWNER TO bl_platform_user;

--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: bl_platform_user
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: model_has_permissions; Type: TABLE; Schema: public; Owner: bl_platform_user
--

CREATE TABLE public.model_has_permissions (
    permission_id bigint NOT NULL,
    model_type character varying(255) NOT NULL,
    model_id bigint NOT NULL
);


ALTER TABLE public.model_has_permissions OWNER TO bl_platform_user;

--
-- Name: model_has_roles; Type: TABLE; Schema: public; Owner: bl_platform_user
--

CREATE TABLE public.model_has_roles (
    role_id bigint NOT NULL,
    model_type character varying(255) NOT NULL,
    model_id bigint NOT NULL
);


ALTER TABLE public.model_has_roles OWNER TO bl_platform_user;

--
-- Name: notifications; Type: TABLE; Schema: public; Owner: bl_platform_user
--

CREATE TABLE public.notifications (
    id uuid NOT NULL,
    type character varying(255) NOT NULL,
    notifiable_type character varying(255) NOT NULL,
    notifiable_id bigint NOT NULL,
    data text NOT NULL,
    read_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.notifications OWNER TO bl_platform_user;

--
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: bl_platform_user
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


ALTER TABLE public.password_reset_tokens OWNER TO bl_platform_user;

--
-- Name: permissions; Type: TABLE; Schema: public; Owner: bl_platform_user
--

CREATE TABLE public.permissions (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    guard_name character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.permissions OWNER TO bl_platform_user;

--
-- Name: permissions_id_seq; Type: SEQUENCE; Schema: public; Owner: bl_platform_user
--

CREATE SEQUENCE public.permissions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.permissions_id_seq OWNER TO bl_platform_user;

--
-- Name: permissions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: bl_platform_user
--

ALTER SEQUENCE public.permissions_id_seq OWNED BY public.permissions.id;


--
-- Name: ports; Type: TABLE; Schema: public; Owner: bl_platform_user
--

CREATE TABLE public.ports (
    id bigint NOT NULL,
    code character varying(3) NOT NULL,
    name character varying(255) NOT NULL,
    last_bl_number integer DEFAULT 0 NOT NULL,
    active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.ports OWNER TO bl_platform_user;

--
-- Name: ports_id_seq; Type: SEQUENCE; Schema: public; Owner: bl_platform_user
--

CREATE SEQUENCE public.ports_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.ports_id_seq OWNER TO bl_platform_user;

--
-- Name: ports_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: bl_platform_user
--

ALTER SEQUENCE public.ports_id_seq OWNED BY public.ports.id;


--
-- Name: role_has_permissions; Type: TABLE; Schema: public; Owner: bl_platform_user
--

CREATE TABLE public.role_has_permissions (
    permission_id bigint NOT NULL,
    role_id bigint NOT NULL
);


ALTER TABLE public.role_has_permissions OWNER TO bl_platform_user;

--
-- Name: roles; Type: TABLE; Schema: public; Owner: bl_platform_user
--

CREATE TABLE public.roles (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    guard_name character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.roles OWNER TO bl_platform_user;

--
-- Name: roles_id_seq; Type: SEQUENCE; Schema: public; Owner: bl_platform_user
--

CREATE SEQUENCE public.roles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.roles_id_seq OWNER TO bl_platform_user;

--
-- Name: roles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: bl_platform_user
--

ALTER SEQUENCE public.roles_id_seq OWNED BY public.roles.id;


--
-- Name: sessions; Type: TABLE; Schema: public; Owner: bl_platform_user
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


ALTER TABLE public.sessions OWNER TO bl_platform_user;

--
-- Name: users; Type: TABLE; Schema: public; Owner: bl_platform_user
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    email_verified_at timestamp(0) without time zone,
    password character varying(255) NOT NULL,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    is_active boolean DEFAULT true NOT NULL,
    phone character varying(30)
);


ALTER TABLE public.users OWNER TO bl_platform_user;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: bl_platform_user
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.users_id_seq OWNER TO bl_platform_user;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: bl_platform_user
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: bills_of_lading id; Type: DEFAULT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.bills_of_lading ALTER COLUMN id SET DEFAULT nextval('public.bills_of_lading_id_seq'::regclass);


--
-- Name: bl_audit_logs id; Type: DEFAULT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.bl_audit_logs ALTER COLUMN id SET DEFAULT nextval('public.bl_audit_logs_id_seq'::regclass);


--
-- Name: bl_cargo_items id; Type: DEFAULT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.bl_cargo_items ALTER COLUMN id SET DEFAULT nextval('public.bl_cargo_items_id_seq'::regclass);


--
-- Name: bl_void_requests id; Type: DEFAULT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.bl_void_requests ALTER COLUMN id SET DEFAULT nextval('public.bl_void_requests_id_seq'::regclass);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: permissions id; Type: DEFAULT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.permissions ALTER COLUMN id SET DEFAULT nextval('public.permissions_id_seq'::regclass);


--
-- Name: ports id; Type: DEFAULT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.ports ALTER COLUMN id SET DEFAULT nextval('public.ports_id_seq'::regclass);


--
-- Name: roles id; Type: DEFAULT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.roles ALTER COLUMN id SET DEFAULT nextval('public.roles_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Data for Name: bills_of_lading; Type: TABLE DATA; Schema: public; Owner: bl_platform_user
--

COPY public.bills_of_lading (id, port_id, sequence_number, bl_number, status, country_origin, shipper_ref, shipper, consignee, notify_party, pre_carriage_by, place_of_receipt, ocean_vessel_voy_no, port_of_discharge, place_of_delivery, final_destination, freight_payable_at, number_of_original_bsl, temperature_control_instructions, freight_type, freight_total, freight_and_charges, excess_value_declaration, on_board_date, delivery_agent, created_by, updated_by, issued_at, voided_by, voided_at, voided_reason, created_at, updated_at, bill_of_loading_ref) FROM stdin;
4	3	1	BAQ-0001	anulado	CALI	PRUEBA	PRUEBA	PRUEBA	PRUEBA	MARITIMO	PR001	25	155	PRUEBA	MINESOTA	MARITIMO	PR001	MUY FRIO	prepaid	4589635.00	LO MAS PRONTO POSIBLE	NO, DIOS LO GUARDA	2026-08-26	RAPPI	1	1	2026-08-26 04:36:52	1	2026-08-26 05:15:50	Esto era una prueba de desarrollo	2026-08-26 04:33:39	2026-08-26 05:15:50	PR001
8	4	1	SMR-0001	emitido	Prueba	Prueba	Esto es una prueba de funcionamiento y generacion de BL's	Esto es una prueba de funcionamiento y generacion de BL's		1000	1000	prueba	Prueba	prueba	prueba15646320	156	10000	-5	prepaid	1000000.00	Prueba		2026-09-04	Prueba	1	1	2026-09-04 14:11:18	\N	\N	\N	2026-09-04 14:09:00	2026-09-04 14:11:18	
5	3	2	BAQ-0002	anulado	CALI	PRUEBA	PRUEBA	PRUEBA	PRUEBA	1	1	1	1	1	1	1	1		prepaid	\N			\N		1	1	2026-08-27 02:06:21	1	2026-08-27 02:20:12	Prueba de admin	2026-08-27 01:13:14	2026-08-27 02:20:12	SD7856214
6	1	1	BVT-0001	anulado			OCEAN FIELD GLOBAL TRADING LIMITED\nROOM S032. 2/F. THE CAPITAL. 61-65 CHATAM\nROAD SOUTH, TSIM SHA TSUI, KOWLOON, HONG KONG	SERLOGISTICA OTM S.A.S NIT: 900.269.583-2/\nDEPOSITO ADUANERO CONSIMEX S.A. NIT:800.156.111-1\nCODIGO MUISCA 2206 CARRERA 4 NORTE NO.52-11\nCALI- COLOMBIA "MERCANCIA EN OTM"\nGILSA S.A.S NIT:900.007.450-8	GILSA SAS NIT:900.007.450-8\nTEL: 3176401177\nCALLE 11 #9-20 OF 700 CC ARISTI\nCALI, COLOMBIA		NINGBO, CHINA	ALBERT P /630E	BUENAVENTURA, COLOMBIA	BUENAVENTURA, COLOMBIA					prepaid	\N			2026-07-30		2	2	2026-08-27 16:20:46	3	2026-08-29 18:52:19	Prueba de anulacion y notificacion	2026-08-27 16:14:48	2026-08-29 18:52:19	STD2606081
7	3	3	BAQ-0003	emitido															prepaid	\N			\N		1	1	2026-08-29 04:14:11	\N	\N	\N	2026-08-28 05:53:25	2026-09-02 04:05:22	
\.


--
-- Data for Name: bl_audit_logs; Type: TABLE DATA; Schema: public; Owner: bl_platform_user
--

COPY public.bl_audit_logs (id, bill_of_lading_id, user_id, action, detail, created_at) FROM stdin;
1	5	1	visto	\N	2026-08-27 02:06:24
2	5	1	descargado	previsualización en navegador	2026-08-27 02:06:49
3	5	1	visto	\N	2026-08-27 02:07:22
4	5	1	visto	\N	2026-08-27 02:07:59
5	4	1	visto	\N	2026-08-27 02:10:57
6	5	1	visto	\N	2026-08-27 02:11:10
7	5	1	visto	\N	2026-08-27 02:13:51
8	5	1	anulacion_solicitada	Prueba de admin	2026-08-27 02:20:13
9	5	1	anulacion_aprobada	Autoaprobado (solicitante es admin, máximo rango).	2026-08-27 02:20:13
10	5	1	visto	\N	2026-08-27 02:25:29
11	5	2	visto	\N	2026-08-27 12:51:25
12	5	2	visto	\N	2026-08-27 12:51:41
13	5	2	visto	\N	2026-08-27 16:08:00
14	6	2	creado	\N	2026-08-27 16:14:49
15	6	2	visto	\N	2026-08-27 16:20:50
16	6	2	descargado	previsualización en navegador	2026-08-27 16:21:09
17	6	2	visto	\N	2026-08-27 16:34:49
18	6	2	visto	\N	2026-08-28 05:32:52
19	7	1	creado	\N	2026-08-28 05:53:25
20	6	2	visto	\N	2026-08-28 21:24:11
21	6	2	descargado	previsualización en navegador	2026-08-28 21:28:12
22	7	1	editado	updated_by	2026-08-29 04:10:02
23	7	1	visto	\N	2026-08-29 04:14:15
24	7	1	descargado	previsualización en navegador	2026-08-29 04:14:32
25	7	2	visto	\N	2026-08-29 04:50:51
26	7	2	anulacion_solicitada	Prueba de notificacion	2026-08-29 04:51:10
27	7	2	visto	\N	2026-08-29 04:51:14
28	7	3	visto	\N	2026-08-29 04:53:09
29	7	3	visto	\N	2026-08-29 05:01:38
30	6	2	visto	\N	2026-08-29 05:03:36
31	6	2	anulacion_solicitada	Prueba de anulacion y notificacion	2026-08-29 05:04:06
32	6	2	visto	\N	2026-08-29 05:04:10
33	7	2	visto	\N	2026-08-29 05:04:44
34	6	3	visto	\N	2026-08-29 05:08:06
35	6	3	visto	\N	2026-08-29 18:52:00
36	6	3	anulacion_aprobada	\N	2026-08-29 18:52:19
37	6	3	visto	\N	2026-08-29 18:52:22
38	7	3	visto	\N	2026-08-29 18:52:43
39	7	3	anulacion_rechazada	No entiendo porque quiere anularse	2026-08-29 18:52:59
40	7	3	visto	\N	2026-08-29 18:53:02
41	7	2	visto	\N	2026-08-29 18:54:13
42	7	2	visto	\N	2026-08-29 19:02:30
43	7	3	visto	\N	2026-08-29 19:03:10
44	6	1	visto	\N	2026-08-30 18:45:36
45	7	1	visto	\N	2026-08-30 18:45:50
46	7	1	visto	\N	2026-08-30 18:46:53
47	7	3	visto	\N	2026-08-30 19:04:02
48	7	2	visto	\N	2026-08-30 19:04:42
49	7	2	anulacion_solicitada	Error de digitacion	2026-08-30 19:04:55
50	7	2	visto	\N	2026-08-30 19:04:58
51	7	3	visto	\N	2026-08-30 19:06:05
52	7	3	visto	\N	2026-08-30 19:08:49
53	7	3	visto	\N	2026-08-30 19:09:15
54	7	3	visto	\N	2026-08-30 19:18:59
55	7	3	anulacion_rechazada	Ser mas específicos con el error por favor	2026-08-30 19:20:13
56	7	3	visto	\N	2026-08-30 19:20:16
57	7	2	visto	\N	2026-08-30 19:21:05
58	7	2	visto	\N	2026-08-30 19:21:52
59	7	2	visto	\N	2026-08-30 19:28:05
60	7	2	anulacion_solicitada	Error de digitación en el peso — ya corregí el dato en el ERP, seguimos necesitando anular este BL porque el consecutivo ya salió ma	2026-08-30 19:28:16
61	7	2	visto	\N	2026-08-30 19:28:18
62	6	1	visto	\N	2026-09-02 04:04:58
63	7	1	visto	\N	2026-09-02 04:05:11
64	7	1	anulacion_rechazada	\N	2026-09-02 04:05:22
65	7	1	visto	\N	2026-09-02 04:05:26
66	7	1	descargado	previsualización en navegador	2026-09-02 04:05:39
67	7	1	visto	\N	2026-09-02 04:12:30
68	7	1	visto	\N	2026-09-02 04:13:35
69	7	1	descargado	previsualización en navegador	2026-09-02 04:14:44
70	7	1	visto	\N	2026-09-03 15:47:34
71	7	1	descargado	previsualización en navegador	2026-09-03 15:47:46
72	7	1	visto	\N	2026-09-03 16:11:51
73	7	1	descargado	previsualización en navegador	2026-09-03 16:12:19
74	7	1	visto	\N	2026-09-03 16:13:54
75	7	1	descargado	previsualización en navegador	2026-09-03 16:14:05
76	7	1	visto	\N	2026-09-03 16:41:36
77	7	1	descargado	previsualización en navegador	2026-09-03 16:42:20
78	7	1	visto	\N	2026-09-03 16:48:45
79	7	1	descargado	previsualización en navegador	2026-09-03 16:49:11
80	7	1	visto	\N	2026-09-03 16:58:27
81	7	1	descargado	previsualización en navegador	2026-09-03 17:02:16
82	7	1	visto	\N	2026-09-03 21:57:38
83	7	1	descargado	previsualización en navegador	2026-09-03 21:57:49
84	7	1	visto	\N	2026-09-04 00:49:02
85	7	1	descargado	previsualización en navegador	2026-09-04 00:49:24
86	7	1	descargado	previsualización en navegador	2026-09-04 01:20:09
87	8	1	creado	\N	2026-09-04 14:09:00
88	8	1	editado	Pre-carriage By, Place of Receipt, Ocean Vessel Voy. No., Port of Discharge, Place of Delivery, Final Destination, updated_by	2026-09-04 14:09:35
89	8	1	editado	Freight Payable At, Number of Original Bs/L, Temperature Control, Freight Total, Freight and Charges, On Board Date, Delivery Agent	2026-09-04 14:11:02
90	8	1	visto	\N	2026-09-04 14:11:21
91	8	1	descargado	previsualización en navegador	2026-09-04 14:11:42
92	7	1	visto	\N	2026-09-04 14:12:11
93	7	1	descargado	previsualización en navegador	2026-09-04 14:12:28
94	7	1	visto	\N	2026-09-04 14:12:59
95	7	2	visto	\N	2026-09-04 14:35:22
\.


--
-- Data for Name: bl_cargo_items; Type: TABLE DATA; Schema: public; Owner: bl_platform_user
--

COPY public.bl_cargo_items (id, bill_of_lading_id, sort_order, marks_and_numbers, container_seal_no, no_of_pkgs, description_of_goods, gross_weight, measurement_cbm, created_at, updated_at) FROM stdin;
3	4	0	PR001	14785954852	1253	UNA SERIE DE ELEMENTOS ELECTRONICOS, COMPUTADORES Y EQUIPOS DE GIMNASIO	8000	26980	2026-08-26 04:36:52	2026-08-26 04:36:52
10	5	0	11111	11	1	CARROS	BMW	PRUEBA	2026-08-27 02:06:21	2026-08-27 02:06:21
13	6	0	N/M No OF ORIGINAL B(s)L SIGNED ONE (1) ORIGINAL	N/M No OF ORIGINAL B(s)L SIGNED ONE (1) ORIGINAL	445 CARTONS	CAAU9967921/ML-CN9068992/40HQ/CY-CY\n445CARTONS/10300.000KGS/68.000CBM	10300.000KGS	68.0000CBM	2026-08-27 16:20:46	2026-08-27 16:20:46
26	7	0				Producto 1 — Sérum Facial Hidratante\n\nSérum facial de textura ligera diseñado para complementar la rutina diaria de cuidado de la piel. Su fórmula está pensada para proporcionar una sensación de hidratación y suavidad sin dejar una sensación pesada o grasosa sobre el rostro. Puede utilizarse como parte de la rutina de mañana y noche, aplicando unas gotas sobre la piel limpia y distribuyendo suavemente hasta su completa absorción. Su presentación práctica facilita el uso diario y permite incorporarlo fácilmente junto con otros productos de limpieza, hidratación y protección facial. Es una alternativa ideal para quienes buscan mantener una rutina sencilla, cómoda y constante, cuidando la apariencia general de la piel y aportándole una sensación fresca y confortable.			2026-08-29 04:14:11	2026-08-29 04:14:11
27	7	1				Producto 2 — Crema Facial Renovadora\n\nCrema facial desarrollada para acompañar las rutinas de cuidado diario y aportar una sensación de confort, hidratación y suavidad. Su textura permite una aplicación uniforme sobre el rostro, facilitando su distribución sin necesidad de utilizar una cantidad excesiva de producto. Puede incorporarse después de la limpieza facial y combinarse con otros productos de tratamiento según las necesidades de cada rutina. Gracias a su formato, resulta práctica tanto para el uso en casa como para complementar rutinas profesionales de cuidado facial. Su aplicación constante ayuda a mantener una sensación agradable en la piel y una apariencia cuidada durante el día.			2026-08-29 04:14:11	2026-08-29 04:14:11
28	7	2				Producto 3 — Protector Solar Facial\n\nProtector solar facial pensado para formar parte de la rutina diaria de cuidado de la piel. Su aplicación permite complementar las medidas habituales de protección frente a la exposición solar y mantener la piel confortable durante las actividades cotidianas. Puede aplicarse como último paso de la rutina facial antes del maquillaje, distribuyendo una cantidad adecuada de manera uniforme sobre las zonas expuestas. Su formato facilita llevarlo y utilizarlo durante el día cuando sea necesario reaplicar. Es un producto recomendado para quienes desean integrar la protección solar como un hábito constante dentro de su rutina de cuidado personal.			2026-08-29 04:14:11	2026-08-29 04:14:11
29	7	3				Producto 4 — Limpiador Facial Suave\n\nLimpiador facial diseñado para ayudar a retirar impurezas, residuos y exceso de grasa acumulados durante el día. Su uso puede integrarse fácilmente tanto en rutinas de mañana como de noche, aplicándolo sobre el rostro y realizando movimientos suaves antes de enjuagar. La limpieza constituye uno de los pasos fundamentales de cualquier rutina facial, ya que permite preparar la piel para la aplicación posterior de sérums, cremas y otros productos. Su presentación está pensada para facilitar un uso práctico y constante, convirtiéndolo en una opción adecuada para quienes buscan mantener una rutina sencilla de cuidado facial.			2026-08-29 04:14:11	2026-08-29 04:14:11
30	7	4				Producto 5 — Kit Completo de Cuidado Facial\n\nKit compuesto por diferentes productos destinados a complementar una rutina integral de cuidado facial. La combinación de productos permite organizar diferentes etapas de la rutina, comenzando con la limpieza y continuando con hidratación y protección. Es una alternativa práctica para quienes prefieren adquirir varios productos complementarios en una sola presentación, evitando tener que seleccionar cada elemento por separado. Su formato también puede resultar conveniente como opción de regalo para personas interesadas en el cuidado personal. Cada producto puede utilizarse siguiendo las indicaciones correspondientes y adaptándose a las características y necesidades particulares de cada tipo de piel.			2026-08-29 04:14:11	2026-08-29 04:14:11
31	7	5				Producto 6 — Mascarilla Facial Hidratante\n\nMascarilla facial creada para complementar ocasionalmente la rutina de cuidado personal y proporcionar una experiencia de aplicación agradable y relajante. Su uso puede incorporarse después de la limpieza facial, permitiendo dedicar unos minutos adicionales al cuidado de la piel. La mascarilla puede utilizarse como parte de una rutina semanal o según las recomendaciones específicas del producto. Su presentación resulta adecuada tanto para uso personal como para complementar servicios profesionales de belleza y cuidado facial. Es una opción especialmente interesante para quienes disfrutan convertir su rutina de cuidado en un momento de relajación y bienestar.			2026-08-29 04:14:11	2026-08-29 04:14:11
35	8	0	159874	151554412101545	10	Esto es una prueba de funcionamiento y generacion de BL's\nEsto es una prueba de funcionamiento y generacion de BL's\nEsto es una prueba de funcionamiento y generacion de BL's\nEsto es una prueba de funcionamiento y generacion de BL's\nEsto es una prueba de funcionamiento y generacion de BL's\nEsto es una prueba de funcionamiento y generacion de BL'sEsto es una prueba de funcionamiento y generacion de BL's\nEsto es una prueba de funcionamiento y generacion de BL's\nEsto es una prueba de funcionamiento y generacion de BL's\nEsto es una prueba de funcionamiento y generacion de BL's\nEsto es una prueba de funcionamiento y generacion de BL's	5444		2026-09-04 14:11:18	2026-09-04 14:11:18
\.


--
-- Data for Name: bl_void_requests; Type: TABLE DATA; Schema: public; Owner: bl_platform_user
--

COPY public.bl_void_requests (id, bill_of_lading_id, requested_by, requested_reason, requested_at, status, resolved_by, resolved_at, resolution_note, created_at, updated_at) FROM stdin;
1	5	1	Prueba de admin	2026-08-27 02:20:12	aprobada	1	2026-08-27 02:20:12	Autoaprobado (solicitante es admin, máximo rango).	2026-08-27 02:20:12	2026-08-27 02:20:12
3	6	2	Prueba de anulacion y notificacion	2026-08-29 05:04:05	aprobada	3	2026-08-29 18:52:19	\N	2026-08-29 05:04:05	2026-08-29 18:52:19
2	7	2	Prueba de notificacion	2026-08-29 04:51:09	rechazada	3	2026-08-29 18:52:59	No entiendo porque quiere anularse	2026-08-29 04:51:09	2026-08-29 18:52:59
4	7	2	Error de digitacion	2026-08-30 19:04:54	rechazada	3	2026-08-30 19:20:12	Ser mas específicos con el error por favor	2026-08-30 19:04:54	2026-08-30 19:20:12
5	7	2	Error de digitación en el peso — ya corregí el dato en el ERP, seguimos necesitando anular este BL porque el consecutivo ya salió ma	2026-08-30 19:28:15	rechazada	1	2026-09-02 04:05:22	\N	2026-08-30 19:28:15	2026-09-02 04:05:22
\.


--
-- Data for Name: cache; Type: TABLE DATA; Schema: public; Owner: bl_platform_user
--

COPY public.cache (key, value, expiration) FROM stdin;
\.


--
-- Data for Name: cache_locks; Type: TABLE DATA; Schema: public; Owner: bl_platform_user
--

COPY public.cache_locks (key, owner, expiration) FROM stdin;
\.


--
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: bl_platform_user
--

COPY public.failed_jobs (id, uuid, connection, queue, payload, exception, failed_at) FROM stdin;
\.


--
-- Data for Name: job_batches; Type: TABLE DATA; Schema: public; Owner: bl_platform_user
--

COPY public.job_batches (id, name, total_jobs, pending_jobs, failed_jobs, failed_job_ids, options, cancelled_at, created_at, finished_at) FROM stdin;
\.


--
-- Data for Name: jobs; Type: TABLE DATA; Schema: public; Owner: bl_platform_user
--

COPY public.jobs (id, queue, payload, attempts, reserved_at, available_at, created_at) FROM stdin;
\.


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: bl_platform_user
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	0001_01_01_000000_create_users_table	1
2	0001_01_01_000001_create_cache_table	1
3	0001_01_01_000002_create_jobs_table	1
4	2026_08_23_000001_create_ports_table	1
5	2026_08_23_000002_create_bills_of_lading_table	1
6	2026_08_23_000003_create_bl_cargo_items_table	1
7	2026_08_24_002542_create_permission_tables	1
8	2026_08_25_000001_add_bill_of_loading_ref_to_bills_of_lading	2
9	2026_08_26_000001_make_sequence_and_bl_number_nullable	3
10	2026_08_27_000001_create_bl_void_requests_table	4
11	2026_08_27_000002_create_bl_audit_logs_table	4
12	2026_08_27_000003_add_pending_void_status_to_bills_of_lading	4
13	2026_08_27_000004_add_is_active_to_users_table	5
14	2026_08_28_000001_create_notifications_table	6
15	2026_08_29_000001_add_phone_to_users_table	7
\.


--
-- Data for Name: model_has_permissions; Type: TABLE DATA; Schema: public; Owner: bl_platform_user
--

COPY public.model_has_permissions (permission_id, model_type, model_id) FROM stdin;
\.


--
-- Data for Name: model_has_roles; Type: TABLE DATA; Schema: public; Owner: bl_platform_user
--

COPY public.model_has_roles (role_id, model_type, model_id) FROM stdin;
4	App\\Models\\User	1
3	App\\Models\\User	2
1	App\\Models\\User	3
\.


--
-- Data for Name: notifications; Type: TABLE DATA; Schema: public; Owner: bl_platform_user
--

COPY public.notifications (id, type, notifiable_type, notifiable_id, data, read_at, created_at, updated_at) FROM stdin;
5e1b9643-4aa7-40a4-83ad-f660d82eba19	App\\Notifications\\VoidRequestSubmitted	App\\Models\\User	3	{"bill_of_lading_id":7,"bl_number":"BAQ-0003","requested_by_name":"Auxiliar Prueba","reason":"Prueba de notificacion","message":"Auxiliar Prueba solicit\\u00f3 anular el BL BAQ-0003"}	2026-08-29 04:53:05	2026-08-29 04:51:10	2026-08-29 04:53:05
d11e588a-c904-4f3f-b827-05f0294663c8	App\\Notifications\\VoidRequestSubmitted	App\\Models\\User	3	{"bill_of_lading_id":6,"bl_number":"BVT-0001","requested_by_name":"Auxiliar Prueba","reason":"Prueba de anulacion y notificacion","message":"Auxiliar Prueba solicit\\u00f3 anular el BL BVT-0001"}	2026-08-29 05:08:00	2026-08-29 05:04:06	2026-08-29 05:08:00
e6297bb1-4506-47b4-b509-3809efedbc36	App\\Notifications\\VoidRequestSubmitted	App\\Models\\User	1	{"bill_of_lading_id":6,"bl_number":"BVT-0001","requested_by_name":"Auxiliar Prueba","reason":"Prueba de anulacion y notificacion","message":"Auxiliar Prueba solicit\\u00f3 anular el BL BVT-0001"}	2026-08-30 18:45:33	2026-08-29 05:04:06	2026-08-30 18:45:33
a975c4b8-62fb-4eda-9329-385483a72eaa	App\\Notifications\\VoidRequestSubmitted	App\\Models\\User	1	{"bill_of_lading_id":7,"bl_number":"BAQ-0003","requested_by_name":"Auxiliar Prueba","reason":"Prueba de notificacion","message":"Auxiliar Prueba solicit\\u00f3 anular el BL BAQ-0003"}	2026-08-30 18:45:49	2026-08-29 04:51:10	2026-08-30 18:45:49
e59d703c-648a-4524-9f7e-6212e044ebde	App\\Notifications\\VoidRequestSubmitted	App\\Models\\User	1	{"bill_of_lading_id":7,"bl_number":"BAQ-0003","requested_by_name":"Auxiliar Prueba","reason":"Error de digitacion","message":"Auxiliar Prueba solicit\\u00f3 anular el BL BAQ-0003"}	\N	2026-08-30 19:04:55	2026-08-30 19:04:55
c84d1a40-f08a-4db6-9e40-d0e0aedda907	App\\Notifications\\VoidRequestSubmitted	App\\Models\\User	3	{"bill_of_lading_id":7,"bl_number":"BAQ-0003","requested_by_name":"Auxiliar Prueba","reason":"Error de digitacion","message":"Auxiliar Prueba solicit\\u00f3 anular el BL BAQ-0003"}	2026-08-30 19:06:02	2026-08-30 19:04:55	2026-08-30 19:06:02
5730a042-f0b0-40f3-908b-184da936ce91	App\\Notifications\\VoidRequestResolved	App\\Models\\User	2	{"bill_of_lading_id":7,"bl_number":"BAQ-0003","approved":false,"reason":"Ser mas espec\\u00edficos con el error por favor","message":"Dayana Ordonez rechaz\\u00f3 tu solicitud de anulaci\\u00f3n del BL BAQ-0003"}	2026-08-30 19:21:02	2026-08-30 19:20:13	2026-08-30 19:21:02
bd3d31d6-fb87-47db-8601-de7c6885e1ab	App\\Notifications\\VoidRequestSubmitted	App\\Models\\User	3	{"bill_of_lading_id":7,"bl_number":"BAQ-0003","requested_by_name":"Auxiliar Prueba","reason":"Error de digitaci\\u00f3n en el peso \\u2014 ya correg\\u00ed el dato en el ERP, seguimos necesitando anular este BL porque el consecutivo ya sali\\u00f3 ma","message":"Auxiliar Prueba solicit\\u00f3 anular el BL BAQ-0003"}	\N	2026-08-30 19:28:16	2026-08-30 19:28:16
8dec7b83-a7ac-48c7-8e19-bf905871af32	App\\Notifications\\VoidRequestSubmitted	App\\Models\\User	1	{"bill_of_lading_id":7,"bl_number":"BAQ-0003","requested_by_name":"Auxiliar Prueba","reason":"Error de digitaci\\u00f3n en el peso \\u2014 ya correg\\u00ed el dato en el ERP, seguimos necesitando anular este BL porque el consecutivo ya sali\\u00f3 ma","message":"Auxiliar Prueba solicit\\u00f3 anular el BL BAQ-0003"}	2026-09-04 14:12:56	2026-08-30 19:28:16	2026-09-04 14:12:56
17fe194e-58ca-4c2d-a85f-f9ff678092f8	App\\Notifications\\VoidRequestResolved	App\\Models\\User	2	{"bill_of_lading_id":7,"bl_number":"BAQ-0003","approved":false,"reason":"Sin motivo de rechazo especificado.","message":"Test User rechaz\\u00f3 tu solicitud de anulaci\\u00f3n del BL BAQ-0003"}	2026-09-04 14:35:19	2026-09-02 04:05:22	2026-09-04 14:35:19
\.


--
-- Data for Name: password_reset_tokens; Type: TABLE DATA; Schema: public; Owner: bl_platform_user
--

COPY public.password_reset_tokens (email, token, created_at) FROM stdin;
test@example.com	$2y$12$BMTxRcdZOnpxi6TtHOqnO.6aqeChPuk1kEKUcs.ZoPiW/5zIgVJYG	2026-08-30 20:11:04
\.


--
-- Data for Name: permissions; Type: TABLE DATA; Schema: public; Owner: bl_platform_user
--

COPY public.permissions (id, name, guard_name, created_at, updated_at) FROM stdin;
1	bl.create	web	2026-08-25 02:32:27	2026-08-25 02:32:27
2	bl.issue	web	2026-08-25 02:32:27	2026-08-25 02:32:27
3	bl.void	web	2026-08-25 02:32:27	2026-08-25 02:32:27
4	bl.view	web	2026-08-25 02:32:27	2026-08-25 02:32:27
5	users.manage	web	2026-08-25 02:32:27	2026-08-25 02:32:27
6	reports.view	web	2026-08-25 02:32:27	2026-08-25 02:32:27
7	bl.void.request	web	2026-08-27 02:13:22	2026-08-27 02:13:22
8	bl.void.approve	web	2026-08-27 02:13:23	2026-08-27 02:13:23
\.


--
-- Data for Name: ports; Type: TABLE DATA; Schema: public; Owner: bl_platform_user
--

COPY public.ports (id, code, name, last_bl_number, active, created_at, updated_at) FROM stdin;
2	CTG	Cartagena	0	t	2026-08-25 02:32:27	2026-08-25 02:32:27
5	TBO	Turbo	0	t	2026-08-25 02:32:27	2026-08-25 02:32:27
1	BVT	Buenaventura	1	t	2026-08-25 02:32:27	2026-08-27 16:20:46
3	BAQ	Barranquilla	3	t	2026-08-25 02:32:27	2026-08-29 04:14:11
4	SMR	Santa Marta	1	t	2026-08-25 02:32:27	2026-09-04 14:11:18
\.


--
-- Data for Name: role_has_permissions; Type: TABLE DATA; Schema: public; Owner: bl_platform_user
--

COPY public.role_has_permissions (permission_id, role_id) FROM stdin;
1	4
2	4
4	4
7	4
8	4
5	4
6	4
1	1
2	1
4	1
7	1
8	1
5	1
6	1
1	2
2	2
4	2
7	2
8	2
6	2
1	3
2	3
4	3
7	3
\.


--
-- Data for Name: roles; Type: TABLE DATA; Schema: public; Owner: bl_platform_user
--

COPY public.roles (id, name, guard_name, created_at, updated_at) FROM stdin;
1	gerencia	web	2026-08-25 02:32:27	2026-08-25 02:32:27
2	coordinacion	web	2026-08-25 02:32:27	2026-08-25 02:32:27
3	auxiliar	web	2026-08-25 02:32:27	2026-08-25 02:32:27
4	admin	web	2026-08-27 02:13:23	2026-08-27 02:13:23
\.


--
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: bl_platform_user
--

COPY public.sessions (id, user_id, ip_address, user_agent, payload, last_activity) FROM stdin;
Co4Sz7O60YXIJHxvmXRw7rIxcx9hnHGqdlt5fx7O	\N	172.19.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36	eyJfdG9rZW4iOiJDcWswUk9iMFdObW5QZVNUS1JkU2p4bmlGbW5QUWtGTjRSVkVkNmRhIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwOTBcL2xvZ2luIiwicm91dGUiOiJsb2dpbiJ9fQ==	1788532815
hSpZyD7BfH1wnZxo35zK9RgsV98A8XgONETIF3Ma	\N	172.19.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36	eyJfdG9rZW4iOiJ0UnAxR1VMTXk2WGJNbEFXY2hySGZZWkhFUDBZQWFORllsS3psTDVCIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwOTBcL2xvZ2luIiwicm91dGUiOiJsb2dpbiJ9fQ==	1788461120
NAt30OZcRTWXHG5yMeJWN9wkH8HHPyz8gGl0wB0s	\N	172.19.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36	eyJfdG9rZW4iOiJIV3pFaVNleXRuWlRnOUlYTVI5czVvVWdtYTd0RlRuWXRlNzVtQUs3IiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwOTBcL2xvZ2luIiwicm91dGUiOiJsb2dpbiJ9fQ==	1788472854
PUmm8MMmmLY1iJQbwMZjxLkZaTsVc8JF1px5UseK	\N	172.19.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36	eyJfdG9rZW4iOiJSWm0zQmFPeDBVR3dRNjBaSjQ3MHBYZXR6U2cyZFhqSDdvYTB1VldzIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwOTBcL2xvZ2luIiwicm91dGUiOiJsb2dpbiJ9fQ==	1788484910
dJtJUfXYuLOu5bsL4NpqmAkPBeo4cDjUJTrgahLx	1	172.18.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36	eyJfdG9rZW4iOiJucUtETDVRdVdLMFVQa2V3ZHgwMzN5SHc4WjNwN2JYek9MeEtoU0dIIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDkwXC9kYXNoYm9hcmQiLCJyb3V0ZSI6ImRhc2hib2FyZCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoxfQ==	1791639351
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: bl_platform_user
--

COPY public.users (id, name, email, email_verified_at, password, remember_token, created_at, updated_at, is_active, phone) FROM stdin;
3	Dayana Ordonez	gerencia@test.com	\N	$2y$12$822jc6ejkXsTSEIMOS4kj.agft.fZpn7qO5UK7EoAJWtW2IdB2GDi	\N	2026-08-28 01:04:03	2026-08-28 01:04:03	t	\N
1	Test User	test@example.com	2026-08-25 02:32:28	$2y$12$ueYXDsz.kCCpKyNJpuUmr.FMzwscvuL3e0q5PKztOWOdVPp4xCYwi	sP860DzMT0NEdtilWgW64nfFqlNy0UtzfqXgzuS1B3viDj9QgrmYOawkOr4T	2026-08-25 02:32:28	2026-08-25 02:32:28	t	\N
2	Auxiliar Prueba	auxiliar@test.com	2026-08-27 02:22:40	$2y$12$ziX26lHSY2xndxVCbU10b.yQ37k9BvbSKIjdLpBUZTJZJQOVJfu56	lgU15yAqg7XAsuB118D38F0PF4AUdkXIPsbhYXSLS9kndfkhF2mp77ZZUAoN	2026-08-27 02:22:40	2026-08-27 02:22:40	t	\N
\.


--
-- Name: bills_of_lading_id_seq; Type: SEQUENCE SET; Schema: public; Owner: bl_platform_user
--

SELECT pg_catalog.setval('public.bills_of_lading_id_seq', 8, true);


--
-- Name: bl_audit_logs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: bl_platform_user
--

SELECT pg_catalog.setval('public.bl_audit_logs_id_seq', 95, true);


--
-- Name: bl_cargo_items_id_seq; Type: SEQUENCE SET; Schema: public; Owner: bl_platform_user
--

SELECT pg_catalog.setval('public.bl_cargo_items_id_seq', 35, true);


--
-- Name: bl_void_requests_id_seq; Type: SEQUENCE SET; Schema: public; Owner: bl_platform_user
--

SELECT pg_catalog.setval('public.bl_void_requests_id_seq', 5, true);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: bl_platform_user
--

SELECT pg_catalog.setval('public.failed_jobs_id_seq', 1, false);


--
-- Name: jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: bl_platform_user
--

SELECT pg_catalog.setval('public.jobs_id_seq', 1, false);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: bl_platform_user
--

SELECT pg_catalog.setval('public.migrations_id_seq', 15, true);


--
-- Name: permissions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: bl_platform_user
--

SELECT pg_catalog.setval('public.permissions_id_seq', 8, true);


--
-- Name: ports_id_seq; Type: SEQUENCE SET; Schema: public; Owner: bl_platform_user
--

SELECT pg_catalog.setval('public.ports_id_seq', 5, true);


--
-- Name: roles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: bl_platform_user
--

SELECT pg_catalog.setval('public.roles_id_seq', 4, true);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: bl_platform_user
--

SELECT pg_catalog.setval('public.users_id_seq', 3, true);


--
-- Name: bills_of_lading bills_of_lading_bl_number_unique; Type: CONSTRAINT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.bills_of_lading
    ADD CONSTRAINT bills_of_lading_bl_number_unique UNIQUE (bl_number);


--
-- Name: bills_of_lading bills_of_lading_pkey; Type: CONSTRAINT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.bills_of_lading
    ADD CONSTRAINT bills_of_lading_pkey PRIMARY KEY (id);


--
-- Name: bills_of_lading bills_of_lading_port_id_sequence_number_unique; Type: CONSTRAINT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.bills_of_lading
    ADD CONSTRAINT bills_of_lading_port_id_sequence_number_unique UNIQUE (port_id, sequence_number);


--
-- Name: bl_audit_logs bl_audit_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.bl_audit_logs
    ADD CONSTRAINT bl_audit_logs_pkey PRIMARY KEY (id);


--
-- Name: bl_cargo_items bl_cargo_items_pkey; Type: CONSTRAINT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.bl_cargo_items
    ADD CONSTRAINT bl_cargo_items_pkey PRIMARY KEY (id);


--
-- Name: bl_void_requests bl_void_requests_pkey; Type: CONSTRAINT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.bl_void_requests
    ADD CONSTRAINT bl_void_requests_pkey PRIMARY KEY (id);


--
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: model_has_permissions model_has_permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.model_has_permissions
    ADD CONSTRAINT model_has_permissions_pkey PRIMARY KEY (permission_id, model_id, model_type);


--
-- Name: model_has_roles model_has_roles_pkey; Type: CONSTRAINT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.model_has_roles
    ADD CONSTRAINT model_has_roles_pkey PRIMARY KEY (role_id, model_id, model_type);


--
-- Name: notifications notifications_pkey; Type: CONSTRAINT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.notifications
    ADD CONSTRAINT notifications_pkey PRIMARY KEY (id);


--
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- Name: permissions permissions_name_guard_name_unique; Type: CONSTRAINT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.permissions
    ADD CONSTRAINT permissions_name_guard_name_unique UNIQUE (name, guard_name);


--
-- Name: permissions permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.permissions
    ADD CONSTRAINT permissions_pkey PRIMARY KEY (id);


--
-- Name: ports ports_code_unique; Type: CONSTRAINT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.ports
    ADD CONSTRAINT ports_code_unique UNIQUE (code);


--
-- Name: ports ports_pkey; Type: CONSTRAINT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.ports
    ADD CONSTRAINT ports_pkey PRIMARY KEY (id);


--
-- Name: role_has_permissions role_has_permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.role_has_permissions
    ADD CONSTRAINT role_has_permissions_pkey PRIMARY KEY (permission_id, role_id);


--
-- Name: roles roles_name_guard_name_unique; Type: CONSTRAINT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_name_guard_name_unique UNIQUE (name, guard_name);


--
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (id);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: bl_audit_logs_bill_of_lading_id_created_at_index; Type: INDEX; Schema: public; Owner: bl_platform_user
--

CREATE INDEX bl_audit_logs_bill_of_lading_id_created_at_index ON public.bl_audit_logs USING btree (bill_of_lading_id, created_at);


--
-- Name: cache_expiration_index; Type: INDEX; Schema: public; Owner: bl_platform_user
--

CREATE INDEX cache_expiration_index ON public.cache USING btree (expiration);


--
-- Name: cache_locks_expiration_index; Type: INDEX; Schema: public; Owner: bl_platform_user
--

CREATE INDEX cache_locks_expiration_index ON public.cache_locks USING btree (expiration);


--
-- Name: failed_jobs_connection_queue_failed_at_index; Type: INDEX; Schema: public; Owner: bl_platform_user
--

CREATE INDEX failed_jobs_connection_queue_failed_at_index ON public.failed_jobs USING btree (connection, queue, failed_at);


--
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: bl_platform_user
--

CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


--
-- Name: model_has_permissions_model_id_model_type_index; Type: INDEX; Schema: public; Owner: bl_platform_user
--

CREATE INDEX model_has_permissions_model_id_model_type_index ON public.model_has_permissions USING btree (model_id, model_type);


--
-- Name: model_has_roles_model_id_model_type_index; Type: INDEX; Schema: public; Owner: bl_platform_user
--

CREATE INDEX model_has_roles_model_id_model_type_index ON public.model_has_roles USING btree (model_id, model_type);


--
-- Name: notifications_notifiable_type_notifiable_id_index; Type: INDEX; Schema: public; Owner: bl_platform_user
--

CREATE INDEX notifications_notifiable_type_notifiable_id_index ON public.notifications USING btree (notifiable_type, notifiable_id);


--
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: bl_platform_user
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: bl_platform_user
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


--
-- Name: bills_of_lading bills_of_lading_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.bills_of_lading
    ADD CONSTRAINT bills_of_lading_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id);


--
-- Name: bills_of_lading bills_of_lading_port_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.bills_of_lading
    ADD CONSTRAINT bills_of_lading_port_id_foreign FOREIGN KEY (port_id) REFERENCES public.ports(id);


--
-- Name: bills_of_lading bills_of_lading_updated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.bills_of_lading
    ADD CONSTRAINT bills_of_lading_updated_by_foreign FOREIGN KEY (updated_by) REFERENCES public.users(id);


--
-- Name: bills_of_lading bills_of_lading_voided_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.bills_of_lading
    ADD CONSTRAINT bills_of_lading_voided_by_foreign FOREIGN KEY (voided_by) REFERENCES public.users(id);


--
-- Name: bl_audit_logs bl_audit_logs_bill_of_lading_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.bl_audit_logs
    ADD CONSTRAINT bl_audit_logs_bill_of_lading_id_foreign FOREIGN KEY (bill_of_lading_id) REFERENCES public.bills_of_lading(id) ON DELETE CASCADE;


--
-- Name: bl_audit_logs bl_audit_logs_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.bl_audit_logs
    ADD CONSTRAINT bl_audit_logs_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id);


--
-- Name: bl_cargo_items bl_cargo_items_bill_of_lading_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.bl_cargo_items
    ADD CONSTRAINT bl_cargo_items_bill_of_lading_id_foreign FOREIGN KEY (bill_of_lading_id) REFERENCES public.bills_of_lading(id) ON DELETE CASCADE;


--
-- Name: bl_void_requests bl_void_requests_bill_of_lading_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.bl_void_requests
    ADD CONSTRAINT bl_void_requests_bill_of_lading_id_foreign FOREIGN KEY (bill_of_lading_id) REFERENCES public.bills_of_lading(id) ON DELETE CASCADE;


--
-- Name: bl_void_requests bl_void_requests_requested_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.bl_void_requests
    ADD CONSTRAINT bl_void_requests_requested_by_foreign FOREIGN KEY (requested_by) REFERENCES public.users(id);


--
-- Name: bl_void_requests bl_void_requests_resolved_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.bl_void_requests
    ADD CONSTRAINT bl_void_requests_resolved_by_foreign FOREIGN KEY (resolved_by) REFERENCES public.users(id);


--
-- Name: model_has_permissions model_has_permissions_permission_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.model_has_permissions
    ADD CONSTRAINT model_has_permissions_permission_id_foreign FOREIGN KEY (permission_id) REFERENCES public.permissions(id) ON DELETE CASCADE;


--
-- Name: model_has_roles model_has_roles_role_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.model_has_roles
    ADD CONSTRAINT model_has_roles_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id) ON DELETE CASCADE;


--
-- Name: role_has_permissions role_has_permissions_permission_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.role_has_permissions
    ADD CONSTRAINT role_has_permissions_permission_id_foreign FOREIGN KEY (permission_id) REFERENCES public.permissions(id) ON DELETE CASCADE;


--
-- Name: role_has_permissions role_has_permissions_role_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: bl_platform_user
--

ALTER TABLE ONLY public.role_has_permissions
    ADD CONSTRAINT role_has_permissions_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

\unrestrict P66ZKlPHeJhIDEoL9HdYos23T9ti4p2DnXQaxn0PPop6iGGhi6za02hoqvyiR8p

