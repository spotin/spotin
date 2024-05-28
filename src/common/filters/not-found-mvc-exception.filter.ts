import {
	ExceptionFilter,
	Catch,
	ArgumentsHost,
	HttpStatus,
} from '@nestjs/common';
import { PrismaClientKnownRequestError } from '@prisma/client/runtime/library';
import { Response } from 'express';

@Catch(PrismaClientKnownRequestError)
export class NotFoundMvcExceptionFilter implements ExceptionFilter {
	catch(exception: PrismaClientKnownRequestError, host: ArgumentsHost) {
		const ctx = host.switchToHttp();
		const response = ctx.getResponse<Response>();

		if (exception.code === 'P2025') {
			response.redirect(HttpStatus.PERMANENT_REDIRECT, '/not-found');
		} else {
			response.redirect(HttpStatus.PERMANENT_REDIRECT, '/');
		}
	}
}
