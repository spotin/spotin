// eslint-disable-next-line @typescript-eslint/no-unused-vars
import session from 'express-session';
import { Token } from '@prisma/client';

declare module 'express-session' {
  export interface SessionData {
    body?: Record<string, string>;
    error?: string;
    errors?: Record<string, string[]>;
    signupWaitingApproval?: boolean;
    token?: Token;
  }
}
