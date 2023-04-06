import {
  Catch,
  ArgumentsHost,
  ConflictException,
  BadRequestException,
  NotFoundException,
  ServiceUnavailableException,
} from '@nestjs/common';
import { BaseExceptionFilter } from '@nestjs/core';
import { PrismaClientKnownRequestError } from '@prisma/client/runtime/library';

const enum PrismaRequestErrors {
  P2000 = 'P2000',
  P2001 = 'P2001',
  P2002 = 'P2002',
  P2003 = 'P2003',
  P2004 = 'P2004',
  P2005 = 'P2005',
  P2006 = 'P2006',
  P2007 = 'P2007',
  P2008 = 'P2008',
  P2009 = 'P2009',
  P2010 = 'P2010',
  P2011 = 'P2011',
  P2012 = 'P2012',
  P2013 = 'P2013',
  P2014 = 'P2014',
  P2015 = 'P2015',
  P2016 = 'P2016',
  P2017 = 'P2017',
  P2018 = 'P2018',
  P2019 = 'P2019',
  P2020 = 'P2020',
  P2021 = 'P2021',
  P2022 = 'P2022',
  // TODO Complete the list of Prisma request errors
  P2025 = 'P2025',
}

@Catch(PrismaClientKnownRequestError)
export class PrismaClientKnownRequestExceptionsFilter extends BaseExceptionFilter {
  catch(exception: PrismaClientKnownRequestError, host: ArgumentsHost): void {
    switch (exception.code) {
      case PrismaRequestErrors.P2001:
      case PrismaRequestErrors.P2015:
      case PrismaRequestErrors.P2018:
      case PrismaRequestErrors.P2025:
        super.catch(new NotFoundException(), host);
        break;

      case PrismaRequestErrors.P2002:
        super.catch(new ConflictException(), host);
        break;

      case PrismaRequestErrors.P2000:
      case PrismaRequestErrors.P2006:
      case PrismaRequestErrors.P2009:
      case PrismaRequestErrors.P2011:
      case PrismaRequestErrors.P2012:
      case PrismaRequestErrors.P2013:
      case PrismaRequestErrors.P2019:
      case PrismaRequestErrors.P2020:
        super.catch(new BadRequestException(), host);
        break;

      case PrismaRequestErrors.P2003:
      case PrismaRequestErrors.P2004:
      case PrismaRequestErrors.P2005:
      case PrismaRequestErrors.P2007:
      case PrismaRequestErrors.P2008:
      case PrismaRequestErrors.P2010:
      case PrismaRequestErrors.P2014:
      case PrismaRequestErrors.P2016:
      case PrismaRequestErrors.P2017:
      case PrismaRequestErrors.P2021:
      case PrismaRequestErrors.P2022:
      default:
        super.catch(new ServiceUnavailableException(), host);
        break;
    }
  }
}
